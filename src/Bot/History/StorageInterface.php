<?php

namespace LLMHub\Bot\History;

/**
 * Контракт для хранилищ истории диалогов.
 * Полностью абстрагирует класс Bot от способа хранения данных.
 */
interface StorageInterface
{
    /**
     * @param string $chatId Уникальный ID диалога.
     * @return array<int, array{role: string, content: string}> История сообщений.
     */
    public function load(string $chatId): array;

    /**
     * @param string $chatId Уникальный ID диалога.
     * @param array<int, array{role: string, content: string}> $history История для сохранения.
     */
    public function save(string $chatId, array $history): void;
}