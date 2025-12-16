<?php

namespace LLMHub\Http;

use LLMHub\Exception\HttpException;

/**
 * Интерфейс для HTTP-клиента.
 * Позволяет в будущем заменить нашу простую реализацию на Guzzle или Symfony HttpClient,
 * не меняя код AI-провайдеров (Dependency Inversion Principle).
 */
interface ClientInterface
{
    /**
     * @param string $url
     * @param array<string, mixed> $body
     * @param array<int, string> $headers
     * @return array<string, mixed>
     * @throws HttpException
     */
    public function post(string $url, array $body, array $headers): array;
}