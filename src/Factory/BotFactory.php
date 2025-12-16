<?php

namespace LLMHub\Factory;

use LLMHub\AI\AiProviderInterface;
use LLMHub\AI\Providers\GeminiProvider;
use LLMHub\AI\Providers\OpenAIProvider;
use LLMHub\AI\Providers\GigaChatProvider;
use LLMHub\Bot\Bot;
use LLMHub\Bot\History\Storage\FileStorage;
use LLMHub\Bot\History\StorageInterface;
use LLMHub\Config\Config;
use LLMHub\Exception\ConfigurationException;
use LLMHub\Http\ClientInterface;
use LLMHub\Http\CurlClient; // <-- Используем наш новый CurlClient
use Psr\Log\LoggerInterface;

final class BotFactory
{
    private readonly Config $config;
    private readonly ?LoggerInterface $logger;
    private ClientInterface $httpClient;

    public function __construct(array $configArray, ?LoggerInterface $logger = null)
    {
        $this->config = new Config($configArray);
        $this->logger = $logger;

        // Создаем наш новый клиент и передаем ему опции из конфига
        $clientOptions = $this->config->get('http_client', []);
        $this->httpClient = new CurlClient($clientOptions);
    }
    
    public function withHttpClient(ClientInterface $client): self
    {
        $this->httpClient = $client;
        return $this;
    }

    public function create(string $chatId): Bot
    {
        return new Bot($chatId, $this->createProvider(), $this->createStorage());
    }

    private function createProvider(): AiProviderInterface
    {
        $providerName = $this->config->get('ai.provider', 'openai');
        switch ($providerName) {
            case 'openai': return new OpenAIProvider($this->httpClient, $this->config, $this->logger);
            case 'gigachat': return new GigaChatProvider($this->httpClient, $this->config, $this->logger);
            case 'gemini': return new GeminiProvider($this->httpClient, $this->config, $this->logger);
            default: throw new ConfigurationException("Unsupported AI provider: {$providerName}");
        }
    }

    private function createStorage(): StorageInterface
    {
        $storageType = $this->config->get('history.storage', 'file');
        switch ($storageType) {
            case 'file':
                $path = $this->config->get('history.path');
                if (!$path) {
                    throw new ConfigurationException('Storage path is not configured for FileStorage.');
                }
                return new FileStorage($path);
            default: throw new ConfigurationException("Unsupported storage type: {$storageType}");
        }
    }
}