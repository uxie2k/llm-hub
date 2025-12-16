# LLMHub

[![Latest Version](https://img.shields.io/packagist/v/uxie2k/llm-hub.svg)](https://packagist.org/packages/uxie2k/llm-hub)

LLMHub — это гибкая, расширяемая PHP-библиотека для интеграции различных больших языковых моделей (LLM) в любой проект. Архитектура построена на принципах SOLID, что позволяет легко добавлять поддержку новых AI-провайдеров без изменения основного кода.

## Ключевые особенности

- **Отсутствие привязки к вендору:** Легко переключайтесь между OpenAI, GigaChat и другими LLM через конфигурацию.
- **Плагинная архитектура:** Добавляйте новые AI-провайдеры или хранилища истории, просто реализуя интерфейс.
- **Принципы SOLID и DI:** Чистый, тестируемый и поддерживаемый код.
- **Готовность к Production:** Настройка через переменные окружения, логирование, кастомные исключения.
- **Простота использования:** Удобная фабрика для быстрой инициализации и начала работы.

## Установка

Установка через Composer:

```bash
composer require uxie2k/llm-hub```

## Использование

```<?php
require 'vendor/autoload.php';

// Загрузка .env файла (рекомендуется)
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

use LLMHub\Factory\BotFactory;

// 1. Конфигурация
$config = [
    'ai' => [
        'provider' => getenv('AI_PROVIDER') ?: 'openai',
    ],
    'openai' => [
        'api_key' => getenv('OPENAI_API_KEY'),
    ],
    'gigachat' => [
        'credentials' => getenv('GIGACHAT_CREDENTIALS'),
    ],
    'history' => [
        'storage' => 'file',
        'path' => __DIR__ . '/storage/ai_history',
    ],
];

// 2. Создание и использование бота
try {
    $factory = new BotFactory($config);
    $bot = $factory->create('user_session_123');
    $response = $bot->chat("Привет, мир!");
    echo $response->text;
} catch (\LLMHub\Exception\LLMHubException $e) {
    // Обработка ошибок
    die("Произошла ошибка: " . $e->getMessage());
}```

## Использование

