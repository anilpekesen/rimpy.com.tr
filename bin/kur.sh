#!/usr/bin/env bash
#
# Yeni bir müşteri sitesini kurar.
#
# Kullanım:
#   bin/kur.sh hidrokom
#
# Bu betik kod tabanını değiştirmez; yalnızca bu kuruluma ait .env dosyasını
# hazırlar, bağımlılıkları yükler ve veritabanını oluşturur. Her müşteri
# kendi sunucusunda, kendi veritabanıyla çalışır.

set -euo pipefail

SITE="${1:-}"

if [[ -z "$SITE" ]]; then
    echo "Kullanım: bin/kur.sh <site-adi>"
    echo "Örnek:    bin/kur.sh hidrokom"
    exit 1
fi

cd "$(dirname "$0")/.."

echo "▸ Bağımlılıklar yükleniyor"
composer install --no-dev --optimize-autoloader --no-interaction

if [[ ! -f .env ]]; then
    echo "▸ .env oluşturuluyor"
    cp .env.example .env

    # Panel adresi her kurulumda farklı ve tahmin edilemez olmalı.
    RASTGELE=$(LC_ALL=C tr -dc 'a-z0-9' < /dev/urandom | head -c 8)
    sed -i.bak "s|^CMS_ADMIN_PATH=.*|CMS_ADMIN_PATH=panel-${RASTGELE}|" .env
    sed -i.bak "s|^DB_DATABASE=.*|DB_DATABASE=${SITE}|" .env
    rm -f .env.bak

    php artisan key:generate --force

    echo
    echo "  .env hazırlandı. Devam etmeden önce şunları doldurun:"
    echo "    APP_NAME, APP_URL, DB_USERNAME, DB_PASSWORD, MAIL_*"
    echo
    read -rp "  Düzenlemeyi bitirdiğinizde Enter'a basın… "
fi

echo "▸ Varlıklar derleniyor"
if command -v npm > /dev/null; then
    npm ci --omit=dev 2>/dev/null || npm install
    npm run build
else
    echo "  npm bulunamadı — public/build klasörünün depoda hazır olduğundan emin olun."
fi

echo "▸ Veritabanı kuruluyor"
php artisan cms:install

echo "▸ Önbellek hazırlanıyor"
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo
echo "✓ ${SITE} kurulumu tamamlandı."
grep '^CMS_ADMIN_PATH=' .env | sed 's|^|  Panel adresi: |'
