<?php

require_once __DIR__ . '/../vendor/autoload.php';

// --- ШАГ 1: ЗАГРУЗКА ПЕРЕМЕННЫХ ОКРУЖЕНИЯ ---
// Загружаем переменные из .env файла, который лежит в корне проекта
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

use LLMHub\Factory\BotFactory;
use LLMHub\Exception\LLMHubException;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// --- ШАГ 2: ЗАГРУЗКА КОНФИГУРАЦИИ ---
// Загружаем массив настроек из отдельного файла
$config = require __DIR__ . '/config.php';

// --- ШАГ 3: ОСНОВНАЯ ЛОГИКА ПРИЛОЖЕНИЯ ---

// Настройка логгера (опционально, но рекомендуется)
$logger = new Logger('LLMHub');
$logger->pushHandler(new StreamHandler(__DIR__ . '/app.log', Logger::DEBUG));

// Уникальный ID чата
$chatId = 'console-user-'.date('Y-m-d');

echo "Бот готов. Введите 'exit' для завершения.\n\n";

try {
    // Создаем фабрику и передаем ей ЗАГРУЖЕННЫЙ конфиг и логгер
    $factory = new BotFactory($config, $logger);
    
    // Создаем экземпляр бота для конкретного чата
    $bot = $factory->create($chatId);

    while (true) {
        $prompt = readline("Вы: ");
        if (in_array($prompt, ['exit', 'quit'])) break;
        if (empty($prompt)) continue;
        
        $response = $bot->chat($prompt);
        echo "Бот: " . $response->text . "\n";
    }

} catch (LLMHubException $e) { // Ловим только наши исключения
    echo "\n[ОШИБКА БИБЛИОТЕКИ]: " . $e->getMessage() . "\n";
    $logger?->critical($e->getMessage(), ['exception' => $e]);
} catch (\Exception $e) { // Остальные, непредвиденные ошибки
    echo "\n[КРИТИЧЕСКАЯ ОШИБКА]: " . $e->getMessage() . "\n";
    $logger?->critical($e->getMessage(), ['exception' => $e]);
}