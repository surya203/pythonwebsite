#!/usr/bin/env python3
"""Confirm optimized images still decode to the locked pixel hashes."""
import hashlib
import json
import sys
from pathlib import Path

from PIL import Image

root = Path(__file__).resolve().parents[1]
manifest = json.loads((Path(__file__).with_name("optimized-image-pixels.json")).read_text())
failures = 0
for rel, expected in manifest.items():
    path = root / rel
    with Image.open(path) as image:
        rgba = image.convert("RGBA")
        digest = hashlib.sha256(rgba.tobytes()).hexdigest()
    if rgba.size != (expected["width"], expected["height"]) or digest != expected["sha256_rgba"]:
        failures += 1
        print(f"FAIL {rel}", file=sys.stderr)
if failures:
    print(f"{failures} pixel mismatch(es)", file=sys.stderr)
    sys.exit(1)
print(f"OK {len(manifest)} optimized images match locked pixels")
