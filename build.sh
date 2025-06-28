#!/bin/bash

echo "🔨 Начинаем сборку проекта..."

# Очистка предыдущей сборки
echo "🧹 Очищаем предыдущую сборку..."
rm -rf public/build

# Установка зависимостей (если нужно)
echo "📦 Проверяем зависимости..."
npm install

# Сборка для продакшена
echo "🔨 Собираем проект..."
npm run build

# Проверка результата
echo "✅ Проверяем результат сборки..."
if [ -f "public/build/manifest.json" ]; then
    echo "✅ Сборка успешна!"
    echo "📁 Файлы в public/build/:"
    ls -la public/build/
else
    echo "❌ Ошибка сборки!"
    exit 1
fi

echo "�� Сборка завершена!" 