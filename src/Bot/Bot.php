<?php

namespace LLMHub\Bot;

use LLMHub\AI\AiProviderInterface;
use LLMHub\AI\Response\AiResponse;
use LLMHub\Bot\History\StorageInterface;

/**
 * Оркестратор бизнес-логики.
 *
 * Демонстрирует:
 * - Dependency Inversion Principle: зависит от абстракций (интерфейсов), а не от реализаций.
 * - Single Responsibility Principle: его единственная задача — управлять процессом
 *   диалога (загрузить историю, вызвать AI, сохранить историю).
 */
final class Bot
{
    private array $history = [];

    public function __construct(
        private readonly string $chatId,
        private readonly AiProviderInterface $aiProvider,
        private readonly StorageInterface $historyStorage
    ) {
        $this->history = $this->historyStorage->load($this->chatId);
    }

    public function chat(string $prompt): AiResponse
    {
        $this->addToHistory('user', $prompt);
        
        $response = $this->aiProvider->chat($prompt, $this->history);

        $this->addToHistory('assistant', $response->text);

        $this->historyStorage->save($this->chatId, $this->history);

        return $response;
    }

    private function addToHistory(string $role, string $content): void
    {
        $this->history[] = ['role' => $role, 'content' => $content];
    }
    
    public function getHistory(): array
    {
        return $this->history;
    }
}