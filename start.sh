#!/bin/bash
cd "$(dirname "$0")"

echo "Starting Laravel dev server on http://localhost:8000 ..."
php artisan serve --port=8000 &>/dev/null &
SERVER_PID=$!

echo "Waiting for server..."
for i in $(seq 1 20); do
  curl -s http://localhost:8000 > /dev/null 2>&1 && break
  sleep 0.5
done

echo "Opening browser (auto-login as admin)..."
open http://localhost:8000/auto-login.html 2>/dev/null \
  || xdg-open http://localhost:8000/auto-login.html 2>/dev/null \
  || echo "Open manually: http://localhost:8000/auto-login.html"

echo "Server PID: $SERVER_PID  (kill $SERVER_PID to stop)"
wait $SERVER_PID
