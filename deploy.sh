#!/bin/bash

# Скрипт для деплоя на продакшен
set -e

echo "🚀 Начинаем деплой на продакшен..."

# 1. Остановка обслуживания (если используется)
echo "📦 Останавливаем обслуживание..."
php artisan down --message="Обновление системы..." --retry=60

# 2. Получение изменений из Git
echo "📥 Получаем изменения из Git..."
git pull origin main

# 3. Установка зависимостей
echo "📦 Устанавливаем PHP зависимости..."
composer install --optimize-autoloader --no-dev

echo "📦 Устанавливаем Node.js зависимости..."
npm install

# 4. Сборка ресурсов для продакшена
echo "🔨 Собираем ресурсы для продакшена..."
npm run build:prod

# 5. Очистка кэша
echo "🧹 Очищаем кэш..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 6. Миграции базы данных
echo "🗄️ Выполняем миграции..."
php artisan migrate --force

# 7. Кэширование для продакшена
echo "⚡ Кэшируем конфигурацию для продакшена..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 8. Настройка прав доступа
echo "🔐 Настраиваем права доступа..."
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# 9. Проверка сборки
echo "✅ Проверяем сборку..."
if [ ! -f "public/build/manifest.json" ]; then
    echo "❌ Ошибка: manifest.json не найден!"
    exit 1
fi

echo "✅ Сборка успешна!"

# 10. Возобновление обслуживания
echo "🔄 Возобновляем обслуживание..."
php artisan up

echo "🎉 Деплой завершен успешно!"
echo "🌐 Сайт доступен по адресу: https://meteo.element-agro.ru"

# 11. Проверка статуса
echo "🔍 Проверяем статус..."
curl -s -o /dev/null -w "%{http_code}" https://meteo.element-agro.ru || echo "Сайт недоступен" 