# LLMHub: The Universal PHP AI Gateway

[![License](https://img.shields.io/github/license/uxie2k/llm-hub?style=flat-square)](https://github.com/uxie2k/llm-hub/blob/main/LICENSE)
[![GitHub last commit](https://img.shields.io/github/last-commit/uxie2k/llm-hub?style=flat-square)](https://github.com/uxie2k/llm-hub/commits/main)
[![GitHub issues](https://img.shields.io/github/issues/uxie2k/llm-hub?style=flat-square)](https://github.com/uxie2k/llm-hub/issues)
[![GitHub stars](https://img.shields.io/github/stars/uxie2k/llm-hub?style=social)](https://github.com/uxie2k/llm-hub/stargazers)

<!-- 
    Packagist.org
    
    [![Latest Version on Packagist](https://img.shields.io/packagist/v/uxie2k/llm-hub.svg?style=flat-square)](https://packagist.org/packages/uxie2k/llm-hub)
    [![Total Downloads](https://img.shields.io/packagist/dt/uxie2k/llm-hub.svg?style=flat-square)](https://packagist.org/packages/uxie2k/llm-hub)
-->

**LLMHub** — это гибкая, расширяемая PHP-библиотека для интеграции различных больших языковых моделей (LLM) в любой проект. Архитектура построена на принципах SOLID, что позволяет легко переключаться между AI-провайдерами и добавлять новых без изменения вашего кода.

---

## ✨ Ключевые особенности

- 🔌 **Полная независимость от вендора:** Переключайтесь между `OpenAI`, `GigaChat`, `Gemini` и другими LLM, изменив всего одну строку в конфигурации.
- 🧩 **Расширяемая архитектура:** Легко добавляйте поддержку новых AI-провайдеров или собственных хранилищ истории диалогов, просто реализуя соответствующие интерфейсы.
- 🛠️ **Профессиональная кодовая база:** Проект построен на принципах SOLID, Dependency Injection и использует строгую типизацию, что гарантирует надежность и простоту поддержки.
- 🚀 **Быстрый старт:** Удобная Фабрика позволяет инициализировать и начать использовать бота всего в несколько строк кода.
- 🛡️ **Безопасность:** Рекомендуется использование переменных окружения (`.env`) для безопасного хранения API-ключей и других секретов.

## Доступные LLM:
- gigachat

## Нет корректных тестов
- openai
- gemini

## 📋 Требования

- PHP 8.1+
- Composer

## 📦 Установка

### Установка напрямую с GitHub (Рекомендуется для приватного использования)

Этот способ идеален, если вы не хотите публиковать пакет на Packagist или хотите использовать его в своих проектах немедленно.

**1. Настройте `composer.json` вашего проекта:**
Добавьте секцию `repositories`, чтобы указать Composer, где найти пакет, и добавьте сам пакет в секцию `require`.

```json
{
    "name": "company/website",
    "require": {
        "php": ">=8.1",
        "vlucas/phpdotenv": "^5.5",
        "uxie2k/llm-hub": "dev-main"
    },
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/uxie2k/llm-hub.git"
        }
    ]
}
```

**2. Выполните установку:**
Откройте терминал в корневой папке вашего проекта и выполните команду:

```bash
composer update
```

## 🚀 Руководство по использованию

**Шаг 1: Настройка окружения (.env)**

Установите зависимость: 

```bash
composer require vlucas/phpdotenv
```

--- Настройки окружения ---
'local' для разработки, 
'production' для боевого сервера
- APP_ENV=

--- Настройки LLMHub ---
Провайдер (gigachat, openai, gemini)
- AI_PROVIDER=

--- Ключи ---
- GIGACHAT_CREDENTIALS=""
- OPENAI_API_KEY=""
- GEMINI_API_KEY=""

**Шаг 2: Создание конфигурационного файла**

config/llmhub.php

```php
return [
    'ai' => [
        'provider' => $_ENV['AI_PROVIDER'] ?? 'gigachat', // Берем из .env, по умолчанию gigachat
    ],
    'gigachat' => [
        'credentials' => $_ENV['GIGACHAT_CREDENTIALS'] ?? null,
        'model' => 'GigaChat:latest',
    ],
    'openai' => [
        'api_key' => $_ENV['OPENAI_API_KEY'] ?? null,
    ],
    'gemini' => [
        'api_key' => isset($_ENV['GEMINI_API_KEY']) ? trim($_ENV['GEMINI_API_KEY']) : null,
        'model' => 'gemini-pro',
    ],
    'history' => [
        'storage' => 'file',
        'path' => __DIR__ . '/../ai_history',
    ],
    'http_client' => [
        'ssl_verify' => ($_ENV['APP_ENV'] ?? 'production') !== 'local',
    ],
];
```

**Шаг 3: Инициализация и использование бота**

```php

// Подключаем Composer для автозагрузки всех классов
require_once __DIR__ . '/vendor/autoload.php';

// Импортируем классы, которые будем использовать
use LLMHub\Factory\BotFactory;
use LLMHub\Exception\LLMHubException;

// 1. ЗАГРУЗКА ОКРУЖЕНИЯ
// Загружаем переменные из .env в суперглобальный массив $_ENV
$dotenv = Dotenv\Dotenv::createUnsafeImmutable(__DIR__);
$dotenv->load();

// 2. ЗАГРУЗКА КОНФИГУРАЦИИ
// Подключаем массив с настройками из отдельного файла
$config = require __DIR__ . '/config/llmhub.php';

// 3. ОСНОВНАЯ ЛОГИКА ПРИЛОЖЕНИЯ
echo "<h1>Production-ready тест LLMHub с GigaChat...</h1>";

try {
    // Создаем экземпляр фабрики, передав ей конфиг
    $factory = new BotFactory($config);

    // Фабрика сама создает и настраивает бота со всеми зависимостями
    $bot = $factory->create('production_test_001');

    $prompt = "Ты GigaChat. Напиши короткое и бодрое приветствие. Сообщи, что ты успешно подключился через профессионально написанную PHP-библиотеку.";
    
    echo "<b>Запрос:</b><p><i>" . htmlspecialchars($prompt) . "</i></p>";
    
    // Отправляем запрос и получаем ответ
    $response = $bot->chat($prompt);

    echo "<b>Ответ от GigaChat:</b>";
    echo "<div style='border: 1px solid #ccc; padding: 10px; background: #f0fff0;'>" . nl2br(htmlspecialchars($response->text)) . "</div>";
    echo "<hr>Тест успешно завершен!";

} catch (LLMHubException $e) {
    // Единая точка обработки всех ошибок, связанных с библиотекой
    echo "<h2 style='color: red;'>Произошла ошибка!</h2>";
    echo "<b>Тип ошибки:</b> " . get_class($e) . "<br>";
    echo "<b>Сообщение:</b> " . $e->getMessage();
}
```
