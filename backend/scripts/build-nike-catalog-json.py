#!/usr/bin/env python3
"""Build nike_catalog.json from the Nike scrape CSV (without Nike CDN image URLs)."""
from __future__ import annotations

import csv
import json
import re
import sys
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
CSV_PATH = ROOT / "database/data/nike_catalog.csv"
OUT_PATH = ROOT / "database/data/nike_catalog.json"


def image_count_from_field(images: str) -> int:
    if not images or not images.strip():
        return 1
    return len([p for p in re.split(r"\s*\|\s*", images.strip()) if p])


def row_to_product(row: dict) -> dict:
    return {
        "url": row.get("url", "").strip(),
        "name": row.get("name", "").strip(),
        "sub_title": row.get("sub_title", "").strip(),
        "brand": row.get("brand", "Nike").strip(),
        "model": row.get("model", "").strip(),
        "color": row.get("color", "").strip(),
        "price": float(row.get("price") or 0),
        "currency": row.get("currency", "USD").strip(),
        "availability": row.get("availability", "InStock").strip(),
        "description": row.get("description", "").strip(),
        "avg_rating": row.get("avg_rating", "").strip() or None,
        "review_count": row.get("review_count", "").strip() or None,
        "available_sizes": row.get("available_sizes", "").strip(),
        "uniq_id": row.get("uniq_id", "").strip(),
        "image_count": image_count_from_field(row.get("images", "")),
    }


def main() -> int:
    if not CSV_PATH.is_file():
        print(f"Missing {CSV_PATH}", file=sys.stderr)
        return 1

    products = []
    with CSV_PATH.open("r", encoding="utf-8", newline="") as fh:
        reader = csv.DictReader(fh)
        for row in reader:
            if not row.get("url"):
                continue
            products.append(row_to_product(row))

    OUT_PATH.write_text(json.dumps(products, indent=2, ensure_ascii=False) + "\n", encoding="utf-8")
    print(f"Wrote {len(products)} products to {OUT_PATH}")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
