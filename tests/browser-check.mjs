/**
 * Headless checks for homepage, reviews, a course page, and contact validation.
 * Uses the system Chrome. Does not submit enquiry mail or open WhatsApp.
 */
import { spawn } from "node:child_process";
import { writeFileSync, mkdirSync } from "node:fs";

const base = process.argv[2] || "http://127.0.0.1:8765";
const outDir = process.argv[3] || "/tmp/visual-checks";
mkdirSync(outDir, { recursive: true });

const chrome = spawn(
  "google-chrome",
  [
    "--headless=new",
    "--disable-gpu",
    "--no-sandbox",
    "--remote-debugging-port=9222",
    "--user-data-dir=/tmp/chrome-profile-latency",
  ],
  { stdio: "ignore" }
);

function sleep(ms) {
  return new Promise((resolve) => setTimeout(resolve, ms));
}

async function cdp() {
  for (let i = 0; i < 40; i++) {
    try {
      const version = await fetch("http://127.0.0.1:9222/json/version");
      if (version.ok) break;
    } catch {
      await sleep(150);
    }
  }
  const created = await fetch("http://127.0.0.1:9222/json/new?about:blank", { method: "PUT" });
  const tab = await created.json();
  const socket = new WebSocket(tab.webSocketDebuggerUrl);
  await new Promise((resolve, reject) => {
    socket.addEventListener("open", resolve);
    socket.addEventListener("error", reject);
  });
  let id = 0;
  const pending = new Map();
  socket.addEventListener("message", (event) => {
    const message = JSON.parse(event.data);
    if (message.id && pending.has(message.id)) {
      const { resolve, reject } = pending.get(message.id);
      pending.delete(message.id);
      if (message.error) reject(new Error(JSON.stringify(message.error)));
      else resolve(message.result);
    }
  });
  function send(method, params = {}) {
    const next = ++id;
    socket.send(JSON.stringify({ id: next, method, params }));
    return new Promise((resolve, reject) => pending.set(next, { resolve, reject }));
  }
  await send("Page.enable");
  await send("Runtime.enable");
  return { send, socket };
}

async function go(send, url) {
  await send("Page.navigate", { url });
  await sleep(1200);
}

async function shot(send, name) {
  const { data } = await send("Page.captureScreenshot", { format: "png" });
  writeFileSync(`${outDir}/${name}.png`, Buffer.from(data, "base64"));
}

async function evalJson(send, expression) {
  const result = await send("Runtime.evaluate", {
    expression,
    returnByValue: true,
    awaitPromise: true,
  });
  if (result.exceptionDetails) {
    throw new Error(JSON.stringify(result.exceptionDetails));
  }
  return result.result.value;
}

const failures = [];
function expect(condition, message) {
  if (!condition) failures.push(message);
  else console.log("OK", message);
}

try {
  const { send } = await cdp();
  await send("Emulation.setDeviceMetricsOverride", {
    width: 1440,
    height: 900,
    deviceScaleFactor: 1,
    mobile: false,
  });

  await go(send, `${base}/index.html`);
  await shot(send, "index-desktop");
  const home = await evalJson(send, `({
    title: document.title,
    h1: document.querySelector("h1") ? document.querySelector("h1").textContent.trim() : "",
    video: !!document.querySelector("video source[src='videos/Digital globe.mp4']"),
    reviews: !!document.querySelector("a[href='https://pythonlife.in/reviews.html']"),
    contact: !!document.querySelector("a[href='contact.html']"),
    bannerHeight: getComputedStyle(document.querySelector(".banner-sec-in")).height
  })`);
  expect(home.h1.includes("Learn Programming"), "homepage heading");
  expect(home.video, "homepage video source");
  expect(home.reviews && home.contact, "homepage reviews and contact links");
  expect(home.bannerHeight === "620px", `desktop banner height ${home.bannerHeight}`);

  await send("Emulation.setDeviceMetricsOverride", {
    width: 390,
    height: 844,
    deviceScaleFactor: 1,
    mobile: true,
  });
  await go(send, `${base}/index.html`);
  await shot(send, "index-mobile");
  const mobileBanner = await evalJson(
    send,
    `getComputedStyle(document.querySelector(".banner-sec-in")).height`
  );
  expect(mobileBanner === "255px", `mobile banner height ${mobileBanner}`);

  await send("Emulation.setDeviceMetricsOverride", {
    width: 1440,
    height: 900,
    deviceScaleFactor: 1,
    mobile: false,
  });
  await go(send, `${base}/reviews.html`);
  await shot(send, "reviews-desktop");
  const reviewImages = await evalJson(send, `document.images.length`);
  expect(reviewImages > 50, `reviews page image count ${reviewImages}`);

  await go(send, `${base}/fullstack-Genai-course-in-telugu.html`);
  await shot(send, "course-desktop");
  const course = await evalJson(send, `({
    form: !!document.querySelector("#enquiry-form"),
    action: !!document.documentElement.innerHTML.includes("send_enquiry.php"),
    thumb: !!document.querySelector("img[src='images/fspai.png']")
  })`);
  expect(course.form && course.action && course.thumb, "course enquiry form and thumbnail");

  await go(send, `${base}/contact.html`);
  await shot(send, "contact-desktop");
  const empty = await evalJson(send, `(() => {
    window.__opened = [];
    window.open = (url) => { window.__opened.push(url); return null; };
    document.getElementById("ctWhatsapp").click();
    return { note: document.getElementById("ctNote").textContent, opened: window.__opened.slice() };
  })()`);
  expect(empty.note === "Please enter your full name. All fields are required.", `empty contact note: ${empty.note}`);
  expect(empty.opened.length === 0, "empty contact form does not open WhatsApp");

  const invalid = await evalJson(send, `(() => {
    document.getElementById("ctName").value = "Fixture Name";
    document.getElementById("ctEmail").value = "not-an-email";
    document.getElementById("ctMobile").value = "0000000000";
    document.getElementById("ctOrg").value = "Fixture College";
    document.getElementById("ctMsg").value = "fixture message not for delivery";
    document.getElementById("ctWhatsapp").click();
    return { note: document.getElementById("ctNote").textContent, opened: window.__opened.slice() };
  })()`);
  expect(invalid.note === "Please enter a valid email address.", `invalid email note: ${invalid.note}`);
  expect(invalid.opened.length === 0, "invalid contact form does not open WhatsApp");

  const valid = await evalJson(send, `(() => {
    document.getElementById("ctEmail").value = "fixture@example.com";
    document.getElementById("ctWhatsapp").click();
    return { note: document.getElementById("ctNote").textContent, opened: window.__opened.slice() };
  })()`);
  expect(valid.opened.length === 1 && valid.opened[0].startsWith("https://wa.me/918143553931?text="), "valid form builds a WhatsApp URL locally");
  expect(valid.opened[0].includes(encodeURIComponent("Fixture Name")), "WhatsApp URL keeps the entered name");
  expect(!valid.opened[0].includes("send_contact_mail"), "contact form does not post mail");
  await shot(send, "contact-valid-desktop");

  await send("Emulation.setDeviceMetricsOverride", {
    width: 390,
    height: 844,
    deviceScaleFactor: 1,
    mobile: true,
  });
  await go(send, `${base}/contact.html`);
  await shot(send, "contact-mobile");
  const contactMobile = await evalJson(send, `({
    form: !!document.getElementById("ctForm"),
    width: document.getElementById("ctForm").getBoundingClientRect().width
  })`);
  expect(contactMobile.form && contactMobile.width > 0 && contactMobile.width <= 390, `contact form fits mobile width ${contactMobile.width}`);
} finally {
  chrome.kill("SIGTERM");
}

if (failures.length) {
  console.error(failures.join("\n"));
  process.exit(1);
}
console.log("browser checks passed");
