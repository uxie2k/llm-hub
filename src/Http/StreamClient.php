<?php

namespace LLMHub\Http;

use LLMHub\Exception\HttpException;

/**
 * Простая реализация HTTP-клиента через stream_context.
 * Реализует ClientInterface.
 */
final class StreamClient implements ClientInterface
{
    public function post(string $url, array $body, array $headers): array
    {
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => implode("\r\n", $headers),
                'content' => json_encode($body),
                'ignore_errors' => true,
            ],
        ]);

        $response = @file_get_contents($url, false, $context);

        if ($response === false) {
            throw new HttpException('HTTP request failed.');
        }

        $responseData = json_decode($response, true);
        $statusCode = $this->parseStatusCode($http_response_header ?? []);

        if ($statusCode >= 400) {
            $errorMessage = $responseData['error']['message'] ?? 'API error';
            throw new HttpException("API request failed with status {$statusCode}: {$errorMessage}", $statusCode);
        }

        return $responseData;
    }

    private function parseStatusCode(array $headers): int
    {
        if (empty($headers[0]) || !preg_match('{HTTP\/\S*\s(\d{3})}', $headers[0], $match)) {
            return 0;
        }
        return (int)$match[1];
    }
}