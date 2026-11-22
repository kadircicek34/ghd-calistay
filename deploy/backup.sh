#!/usr/bin/env bash
set -euo pipefail

BACKUP_DIR="${BACKUP_DIR:-/root/çalıştay/backups}"
STAMP="$(date +%Y%m%d-%H%M%S)"
VOLUME="${VOLUME:-ghdcalistay_data}"

mkdir -p "$BACKUP_DIR"
docker run --rm \
  -v "${VOLUME}:/data:ro" \
  -v "${BACKUP_DIR}:/backup" \
  alpine:3.24 \
  tar czf "/backup/ghdcalistay-${STAMP}.tgz" -C /data .

echo "Backup written: ${BACKUP_DIR}/ghdcalistay-${STAMP}.tgz"
