<?php

namespace LLMHub\Bot\History\Storage;

use LLMHub\Bot\History\StorageInterface;
use LLMHub\Exception\ConfigurationException;

final class FileStorage implements StorageInterface
{
    private readonly string $storagePath;

    public function __construct(string $storagePath)
    {
        if (empty($storagePath)) {
            throw new ConfigurationException('FileStorage path cannot be empty.');
        }
        if (!is_dir($storagePath) && !@mkdir($storagePath, 0775, true)) {
            throw new \RuntimeException("Failed to create storage directory: {$storagePath}");
        }
        if (!is_writable($storagePath)) {
             throw new \RuntimeException("Storage directory is not writable: {$storagePath}");
        }
        $this->storagePath = rtrim($storagePath, '/\\');
    }

    public function load(string $chatId): array
    {
        $file = $this->getFilePath($chatId);
        if (!file_exists($file)) {
            return [];
        }
        return json_decode(file_get_contents($file), true) ?: [];
    }

    public function save(string $chatId, array $history): void
    {
        file_put_contents($this->getFilePath($chatId), json_encode($history, JSON_PRETTY_PRINT));
    }

    private function getFilePath(string $chatId): string
    {
        $safeChatId = preg_replace('/[^a-zA-Z0-9_-]/', '', $chatId);
        return $this->storagePath . DIRECTORY_SEPARATOR . $safeChatId . '.json';
    }
}