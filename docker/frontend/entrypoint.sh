#!/bin/sh
set -e

cd /app

echo "==> Installing dependencies..."
npm install --no-fund --no-audit

echo "==> Starting Quasar dev server on 0.0.0.0:9000..."
exec quasar dev --hostname 0.0.0.0
