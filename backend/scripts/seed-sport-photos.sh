#!/usr/bin/env bash
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
DST="$ROOT/public/free-catalog/men/sport"
Q="auto=format&fit=crop&w=900&h=1125&q=85"

APPAREL="photo-1571019614242-c5c5dee9f50b photo-1534438327276-14e5300c3a48 photo-1517836357463-d25dfeac3438 photo-1556821840-3a63f95609a7 photo-1518611012118-696072aa579a photo-1571019613454-1cb2f99b2d8b photo-1434389677669-e08b4cac3105 photo-1521572163474-6864f9cf17ab photo-1506629082955-511b1aa562c8 photo-1552346154-21d32810aba3"
FOOTWEAR="photo-1542291026-7eec264c27ff photo-1606107557195-0e29a4b5b4aa photo-1549298916-b41d501d3772 photo-1556906781-9a412961c28c photo-1608231387042-66d1773070a5 photo-1595950653106-6c9ebd614d3a"

download_family() {
  local family="$1"
  local offset="$2"
  local pool="$3"
  read -r -a ids <<< "$pool"
  local dir="$DST/$family"
  mkdir -p "$dir"
  rm -f "$dir"/*.jpg
  for n in $(seq 1 20); do
    local idx=$(( (offset + n - 1) % ${#ids[@]} ))
    local id="${ids[$idx]}"
    curl -fsSL "https://images.unsplash.com/${id}?${Q}" -o "$dir/$(printf '%02d.jpg' "$n")"
  done
  echo "  $family: 20 photos"
}

echo "Downloading sport catalog photos..."
i=0
for family in training_tee compression_top hoodie track_jacket joggers shorts basketball_jersey football_jersey running_tank windbreaker; do
  download_family "$family" "$i" "$APPAREL"
  i=$((i + 2))
done
i=0
for family in running_shoes basketball_shoes sneakers training_shoes slides; do
  download_family "$family" "$i" "$FOOTWEAR"
  i=$((i + 1))
done
echo "Done."
