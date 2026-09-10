#!/usr/bin/env bash
#
# Çekirdeği günceller. Kurulu bir sitede çalıştırılır.
#
# Sırayla: bakım kipine al → kodu çek → bağımlılıkları tazele →
# veritabanını göçür → varlıkları derle → önbelleği yenile → yayına al.
# Herhangi bir adım hata verirse site bakım kipinden çıkarılır ve
# ziyaretçi yarım kalmış bir kurulumla karşılaşmaz.

set -euo pipefail

cd "$(dirname "$0")/.."

yayina_al() {
    php artisan up > /dev/null 2>&1 || true
}
trap yayina_al EXIT

echo "▸ Bakım kipine alınıyor"
php artisan down --render="errors::503" --retry=60 || true

echo "▸ Yedek alınıyor"
mkdir -p storage/backups
TARIH=$(date +%Y%m%d-%H%M%S)
php artisan db:dump --path="storage/backups/${TARIH}.sql" 2>/dev/null \
    || echo "  (db:dump yok — veritabanı yedeğini hosting panelinden alın)"

echo "▸ Kod çekiliyor"
git pull --ff-only

echo "▸ Bağımlılıklar"
composer install --no-dev --optimize-autoloader --no-interaction

echo "▸ Veritabanı göçleri"
php artisan migrate --force

echo "▸ Varlıklar"
if command -v npm > /dev/null; then
    npm ci --omit=dev 2>/dev/null || npm install
    npm run build
fi

echo "▸ Önbellek yenileniyor"
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✓ Güncelleme tamamlandı."
