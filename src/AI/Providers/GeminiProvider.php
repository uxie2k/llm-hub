<?php

namespace LLMHub\AI\Providers;

use LLMHub\AI\Response\AiResponse;
use LLMHub\Exception\ConfigurationException;
use LLMHub\Exception\ProviderException;

final class GeminiProvider extends AbstractProvider
{
    // Базовый URL для Gemini API
    private const API_BASE_URL = 'https://generativelanguage.googleapis.com/v1/models/';

    public function chat(string $prompt, array $history = []): AiResponse
    {
        $apiKey = $this->config->get('gemini.api_key');
        if (!$apiKey) {
            throw new ConfigurationException('Gemini API key is not configured.');
        }

        $model = $this->config->get('gemini.model', 'gemini-pro');
        $apiUrl = self::API_BASE_URL . $model . ':generateContent?key=' . $apiKey;

        $headers = ['Content-Type: application/json'];
        
        // Gemini имеет другой формат для тела запроса
        $body = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ]
        ];

        try {
            $this->logger->info('Sending request to Gemini API.');
            $response = $this->httpClient->post($apiUrl, $body, $headers);
            $this->logger->info('Received successful response from Gemini API.');
        } catch (\Exception $e) {
            $this->logger->error('Gemini API request failed.', ['exception' => $e]);
            throw new ProviderException('Gemini API request failed: ' . $e->getMessage(), 0, $e);
        }

        // И другой формат ответа
        $responseText = $response['candidates'][0]['content']['parts'][0]['text'] ?? '';
        $metadata = ['finishReason' => $response['candidates'][0]['finishReason'] ?? 'UNKNOWN'];

        return new AiResponse($responseText, $metadata);
    }
}