<?php
/**
 * Behaviour locks for the imported site.
 * Runs offline: PHP syntax, recent-activity JSON, contact-mail capture, and
 * static form/route contracts. It does not open a production database or
 * deliver mail.
 */
declare(strict_types=1);

$root = dirname(__DIR__);
$failures = 0;

function fail(string $message): void
{
    global $failures;
    $failures++;
    fwrite(STDERR, "FAIL: {$message}\n");
}

function ok(string $message): void
{
    fwrite(STDOUT, "OK: {$message}\n");
}

$phpFiles = [
    'config.php',
    'index.php',
    'mypltdata2022.php',
    'send_contact_mail.php',
    'send_enquiry.php',
    'api/recent-activity.php',
];
foreach ($phpFiles as $relative) {
    $lint = shell_exec('php -l ' . escapeshellarg($root . '/' . $relative) . ' 2>&1');
    if (!is_string($lint) || !str_contains($lint, 'No syntax errors')) {
        fail("php -l {$relative}: " . trim((string) $lint));
    }
}
ok('PHP syntax');

$config = file_get_contents($root . '/config.php');
foreach (['DB_HOST', 'DB_USER', 'DB_PASSWORD', 'DB_NAME'] as $name) {
    if (!str_contains($config, "getenv('{$name}')")) {
        fail("config.php missing getenv {$name}");
    }
}
if (preg_match("/mysqli_connect\\(\\s*'/", $config) === 1) {
    fail('config.php contains a literal mysqli host');
}
ok('config.php uses environment variables');

$activitySource = file_get_contents($root . '/api/recent-activity.php');
preg_match_all(
    '/"name"\\s*=>\\s*"((?:\\\\.|[^"\\\\])*)".*?"location"\\s*=>\\s*"((?:\\\\.|[^"\\\\])*)".*?"course"\\s*=>\\s*"((?:\\\\.|[^"\\\\])*)".*?"time"\\s*=>\\s*"((?:\\\\.|[^"\\\\])*)"/s',
    $activitySource,
    $matches,
    PREG_SET_ORDER
);
if (count($matches) !== 50) {
    fail('recent-activity source record count is ' . count($matches) . ', expected 50');
}
$known = [];
foreach ($matches as $match) {
    $known[$match[1] . '|' . $match[2] . '|' . $match[3] . '|' . $match[4]] = true;
}
if (!str_contains($activitySource, 'Cache-Control: no-store, no-cache, must-revalidate, max-age=0')) {
    fail('recent-activity lost no-store cache header');
}
for ($i = 0; $i < 40; $i++) {
    $raw = shell_exec('php ' . escapeshellarg($root . '/api/recent-activity.php') . ' 2>/dev/null');
    $decoded = json_decode((string) $raw, true);
    if (!is_array($decoded) || ($decoded['success'] ?? null) !== true || !isset($decoded['data'])) {
        fail('recent-activity response is not a success payload');
        break;
    }
    $data = $decoded['data'];
    $keys = array_keys($data);
    sort($keys);
    if ($keys !== ['course', 'location', 'name', 'time']) {
        fail('recent-activity data keys changed: ' . implode(',', $keys));
        break;
    }
    $id = $data['name'] . '|' . $data['location'] . '|' . $data['course'] . '|' . $data['time'];
    if (!isset($known[$id])) {
        fail('recent-activity returned a record that is not in the source list');
        break;
    }
}
ok('recent-activity contract');

$capture = tempnam(sys_get_temp_dir(), 'mail-capture-');
$sendmail = tempnam(sys_get_temp_dir(), 'fake-sendmail-');
file_put_contents($sendmail, "#!/bin/sh\ncat > " . escapeshellarg($capture) . "\nexit 0\n");
chmod($sendmail, 0755);
$mailRunner = tempnam(sys_get_temp_dir(), 'mail-runner-');
file_put_contents(
    $mailRunner,
    "<?php\n\$_POST = [\n" .
    "  'name' => 'Fixture Name',\n" .
    "  'email' => 'fixture@example.com',\n" .
    "  'mobile' => '0000000000',\n" .
    "  'message' => 'fixture message not for delivery',\n" .
    "];\nrequire " . var_export($root . '/send_contact_mail.php', true) . ";\n"
);
$mailOutput = shell_exec(
    'php -d sendmail_path=' . escapeshellarg($sendmail) . ' ' . escapeshellarg($mailRunner) . ' 2>&1'
);
$mailJson = json_decode((string) $mailOutput, true);
if (!is_array($mailJson) || ($mailJson['status'] ?? null) !== 'success') {
    fail('send_contact_mail fixture did not return success: ' . trim((string) $mailOutput));
} else {
    $captured = is_file($capture) ? file_get_contents($capture) : '';
    foreach (['Fixture Name', 'fixture@example.com', '0000000000', 'fixture message not for delivery', 'info@pythonlife.in'] as $needle) {
        if (!str_contains((string) $captured, $needle)) {
            fail("captured mail missing {$needle}");
        }
    }
    if (($mailJson['message'] ?? '') !== 'Thank You for contacting us. We Will get back to you soon.') {
        fail('send_contact_mail success message changed');
    }
}
@unlink($mailRunner);
@unlink($sendmail);
@unlink($capture);
ok('send_contact_mail local capture');

$enquiry = shell_exec('php ' . escapeshellarg($root . '/send_enquiry.php') . ' 2>&1');
if (str_contains((string) $enquiry, 'Thank You for contacting us, We will get back to you soon')) {
    fail('send_enquiry.php reported success without a local fixture database');
}
ok('send_enquiry.php does not succeed without a database');

$contact = file_get_contents($root . '/contact.html');
foreach ([
    'id="ctForm"',
    'id="ctName"',
    'id="ctEmail"',
    'id="ctMobile"',
    'id="ctOrg"',
    'id="ctMsg"',
    'id="ctWhatsapp"',
    'pattern="[0-9+\s\-]{10,15}"',
    'Please enter your ',
    'Please enter a valid ',
    'https://wa.me/918143553931?text=',
] as $needle) {
    if (!str_contains($contact, $needle)) {
        fail("contact.html missing {$needle}");
    }
}
ok('contact form validation contract');

$enquiryPage = file_get_contents($root . '/fullstack-Genai-course-in-telugu.html');
foreach (['id="enquiry-form"', 'url:"send_enquiry.php"', "var required = ['name','email','mobile'];"] as $needle) {
    if (!str_contains($enquiryPage, $needle)) {
        fail("enquiry form contract missing {$needle}");
    }
}
ok('course enquiry request contract');

$routes = [
    'index.html',
    'contact.html',
    'reviews.html',
    'about_us.html',
    'python-course-in-telugu.html',
    'fullstack-Genai-course-in-telugu.html',
    'css/styles.css',
    'css/responsive.css',
    'js/main.js',
    'images/logo.png',
    'images/fspai.png',
    'videos/Digital globe.mp4',
    'sitemap.xml',
];
foreach ($routes as $relative) {
    if (!is_file($root . '/' . $relative)) {
        fail("missing route asset {$relative}");
    }
}
ok('critical files exist');

$index = file_get_contents($root . '/index.html');
foreach ([
    'videos/Digital globe.mp4',
    'api/recent-activity.php',
    'href="contact.html"',
    'href="https://pythonlife.in/reviews.html"',
    'href="https://pythonlife.in/contact.html"',
    'href="python-course-in-telugu.html"',
    'href="https://pythonlife.in/python-course-in-telugu.html"',
] as $needle) {
    if (!str_contains($index, $needle)) {
        fail("index.html missing {$needle}");
    }
}
ok('homepage navigation and media contracts');

if ($failures > 0) {
    fwrite(STDERR, "{$failures} failure(s)\n");
    exit(1);
}
fwrite(STDOUT, "all regression checks passed\n");
