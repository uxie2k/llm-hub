<?php

/**
 * Файл конфигурации для LLMHub.
 * Его единственная задача - вернуть массив с настройками.
 */

return [
    'ai' => [
        // Управляется через переменную окружения, по умолчанию 'gigachat'
        'provider' => getenv('AI_PROVIDER') ?: 'gigachat',
    ],

    'openai' => [
        // Мы просто берем значение. Если его нет, getenv вернет false.
        'api_key' => getenv('OPENAI_API_KEY'),
        'model' => 'gpt-4o',
    ],

    'gigachat' => [
        'credentials' => getenv('GIGACHAT_CREDENTIALS'),
        'model' => 'GigaChat:latest',
    ],

    'history' => [
        'storage' => 'file',
        // Путь теперь корректный, так как он находится в той же папке
        'path' => __DIR__ . '/history_storage',
    ],
];