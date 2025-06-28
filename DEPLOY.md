# Инструкции по деплою на продакшен

## 1. Подготовка сервера

### Установка зависимостей
```bash
composer install --optimize-autoloader --no-dev
npm install
```

### Настройка окружения
```bash
cp .env.example .env
php artisan key:generate
```

### Настройка .env для продакшена
```env
APP_NAME="Meteo El-Agro"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://meteo.element-agro.ru

# База данных
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=meteo_el_agro
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Настройки для продакшена
ASSET_URL=https://meteo.element-agro.ru
VITE_BASE_URL=https://meteo.element-agro.ru
```

## 2. Сборка ресурсов

### Сборка для продакшена
```bash
npm run build:prod
```

### Проверка сборки
```bash
ls -la public/build/
```

Должны быть файлы:
- `manifest.json`
- `app-[hash].js`
- `app-[hash].css`

## 3. Настройка веб-сервера

### Nginx конфигурация
```nginx
server {
    listen 80;
    server_name meteo.element-agro.ru;
    root /path/to/your/project/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

## 4. Настройка Laravel

### Кэширование конфигурации
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Миграции и сидеры
```bash
php artisan migrate --force
php artisan db:seed --class=FarmSeeder --force
```

### Права доступа
```bash
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

## 5. Проверка

### Проверка загрузки ресурсов
Откройте https://meteo.element-agro.ru и проверьте в DevTools:
- Нет ошибок 404 для JS/CSS файлов
- Ресурсы загружаются с правильного домена
- Нет ошибок CORS

### Проверка API
```bash
curl -H "Accept: application/json" https://meteo.element-agro.ru/api/v1/farms
```

## 6. Мониторинг

### Логи
```bash
tail -f storage/logs/laravel.log
```

### Статус сервисов
```bash
systemctl status nginx
systemctl status php8.1-fpm
systemctl status mysql
```

## Устранение проблем

### Если ресурсы не загружаются:
1. Проверьте, что `public/build/` содержит файлы
2. Проверьте права доступа к папке `public/build/`
3. Проверьте конфигурацию Nginx
4. Проверьте .env файл (ASSET_URL)

### Если API не работает:
1. Проверьте настройки CORS в `config/cors.php`
2. Проверьте права доступа к storage
3. Проверьте логи Laravel 