#!/bin/sh
set -e

: "${APP_BASE_DIR:=/var/www/html}"
: "${DB_CONNECTION:=sqlite}"

script_name="marreta-init"

if [ "$DB_CONNECTION" = "sqlite" ]; then
    db_file="${DB_DATABASE:-$APP_BASE_DIR/storage/app/database.sqlite}"

    if [ ! -f "$db_file" ]; then
        mkdir -p "$(dirname "$db_file")"
        touch "$db_file"
        echo "🗄️  ($script_name): Created SQLite database at $db_file"
    fi
fi

if [ -z "${APP_KEY:-}" ]; then
    key_file="$APP_BASE_DIR/storage/app/app.key"

    if [ ! -s "$key_file" ]; then
        php "$APP_BASE_DIR/artisan" key:generate --show > "$key_file"
        echo "🔑 ($script_name): No APP_KEY provided, generated one at $key_file"
    fi

    printf 'APP_KEY=%s\n' "$(cat "$key_file")" > "$APP_BASE_DIR/.env"
else
    rm -f "$APP_BASE_DIR/.env"
fi

exit 0
