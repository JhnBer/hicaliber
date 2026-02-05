#!/bin/sh

set -e

cleanup() {
    echo "Stopping processes..."
    [ ! -z "$NPM_PID" ] && kill -TERM "$NPM_PID" 2>/dev/null || true
    [ ! -z "$OCTANE_PID" ] && kill -TERM "$OCTANE_PID" 2>/dev/null || true
    [ ! -z "$REVERB_PID" ] && kill -TERM "$REVERB_PID" 2>/dev/null || true
    exit 0
}

trap cleanup INT TERM


echo "> Development mode"

php artisan octane:stop > /dev/null 2>&1 || true

if [ -f "bootstrap/octane/octane-server-state.json" ]; then
    rm -f bootstrap/octane/octane-server-state.json
fi

#    sleep 2

pnpm run dev &
NPM_PID=$!

echo "> -starting reverb"
php artisan reverb:start &
REVERB_PID=$!

php artisan queue:work redis &

#    (sleep 5 &&
php artisan octane:start -vv --port=9000 --server=swoole --host=0.0.0.0 --watch
#    ) &
OCTANE_PID=$!

wait -n $NPM_PID $OCTANE_PID $REVERB_PID

kill -TERM $NPM_PID $OCTANE_PID $REVERB_PID 2>/dev/null
wait -n $NPM_PID $OCTANE_PID $REVERB_PID 2>/dev/null || true

#echo "> Ready. All services are disabled."
#tail -f /dev/null

