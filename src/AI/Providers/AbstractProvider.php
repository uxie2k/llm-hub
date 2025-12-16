<?php

namespace LLMHub\AI\Providers;

use LLMHub\AI\AiProviderInterface;
use LLMHub\Config\Config;
use LLMHub\Http\ClientInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Абстрактный базовый класс для провайдеров.
 * Демонстрирует Open/Closed Principle: общая логика (DI, логирование) реализована здесь,
 * а специфичная для каждого провайдера логика будет в дочерних классах.
 * Мы расширяем функциональность, не изменяя этот класс.
 */
abstract class AbstractProvider implements AiProviderInterface
{
    protected ClientInterface $httpClient;
    protected Config $config;
    protected LoggerInterface $logger;

    public function __construct(
        ClientInterface $httpClient,
        Config $config,
        ?LoggerInterface $logger = null
    ) {
        $this->httpClient = $httpClient;
        $this->config = $config;
        $this->logger = $logger ?? new NullLogger();
    }
}