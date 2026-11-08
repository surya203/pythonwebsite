#!/usr/bin/env python3
"""Time representative responses from a local server.

Usage:
  php -S 127.0.0.1:8765 -t . 
  python3 tests/benchmark.py http://127.0.0.1:8765

The PHP built-in server does not compress responses. Pass --limit-rate to
approximate a constrained link; curl applies that cap after its initial burst,
so small files may still finish immediately.
"""
import argparse
import json
import statistics
import subprocess
import sys

PATHS = [
    "/index.html",
    "/fullstack-Genai-course-in-telugu.html",
    "/python-course-in-telugu.html",
    "/reviews.html",
    "/contact.html",
    "/css/styles.css",
    "/css/responsive.css",
    "/js/main.js",
    "/api/recent-activity.php",
    "/images/logo.png",
    "/images/fspai.png",
    "/images/deevveevv.png",
    "/images/graphic_thumbnail_web.png",
    "/videos/Digital%20globe.mp4",
    "/sitemap.xml",
    "/send_contact_mail.php",
]


def sample(base: str, path: str, limit_rate: int | None, runs: int) -> dict:
    totals = []
    ttfbs = []
    size = None
    code = None
    for _ in range(runs):
        cmd = [
            "curl",
            "-s",
            "-o",
            "/dev/null",
            "-w",
            "%{http_code} %{size_download} %{time_starttransfer} %{time_total}",
            "--max-time",
            "180",
        ]
        if limit_rate:
            cmd.extend(["--limit-rate", str(limit_rate)])
        cmd.append(base.rstrip("/") + path)
        completed = subprocess.run(cmd, capture_output=True, text=True, check=False)
        parts = completed.stdout.split()
        if len(parts) != 4:
            raise SystemExit(f"curl failed for {path}: {completed.stdout!r} {completed.stderr!r}")
        code = int(parts[0])
        size = int(parts[1])
        ttfbs.append(float(parts[2]))
        totals.append(float(parts[3]))
    return {
        "runs": runs,
        "http_code": code,
        "bytes": size,
        "median_ttfb_s": statistics.median(ttfbs),
        "median_total_s": statistics.median(totals),
        "min_total_s": min(totals),
        "max_total_s": max(totals),
    }


def main() -> int:
    parser = argparse.ArgumentParser()
    parser.add_argument("base", nargs="?", default="http://127.0.0.1:8765")
    parser.add_argument("--runs", type=int, default=3)
    parser.add_argument("--limit-rate", type=int, default=0)
    args = parser.parse_args()
    report = {
        "base": args.base,
        "limit_rate_bytes_per_sec": args.limit_rate or None,
        "paths": {},
    }
    for path in PATHS:
        report["paths"][path] = sample(args.base, path, args.limit_rate or None, args.runs)
        print(path, report["paths"][path], flush=True)
    json.dump(report, sys.stdout, indent=2)
    sys.stdout.write("\n")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
