<?php

/**
 * Файл конфигурации для LLMHub.
 * Его единственная задача - вернуть массив с настройками.
 */

return [
    'ai' => [
        'provider' => $_ENV['AI_PROVIDER'] ?? 'gigachat', // Берем из .env, по умолчанию gigachat
    ],
    'gigachat' => [
        'credentials' => $_ENV['GIGACHAT_CREDENTIALS'] ?? null,
        'model' => 'GigaChat:latest',
    ],
    'openai' => [ // Настройки для других AI тоже могут быть здесь
        'api_key' => $_ENV['OPENAI_API_KEY'] ?? null,
    ],
    'gemini' => [
        'api_key' => isset($_ENV['GEMINI_API_KEY']) ? trim($_ENV['GEMINI_API_KEY']) : null,
        'model' => 'gemini-pro',
    ],
    'history' => [
        'storage' => 'file',
        'path' => __DIR__ . '/../ai_history', // Путь от файла конфига
    ],
    'http_client' => [
        'ssl_verify' => ($_ENV['APP_ENV'] ?? 'production') !== 'local', // Умное управление SSL
    ],
];