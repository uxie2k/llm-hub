<?php

namespace LLMHub\AI\Providers;

use LLMHub\AI\Response\AiResponse;
use LLMHub\Exception\ConfigurationException;
use LLMHub\Exception\ProviderException;

final class OpenAIProvider extends AbstractProvider
{
    private const API_URL = 'https://api.openai.com/v1/chat/completions';

    public function chat(string $prompt, array $history = []): AiResponse
    {
        $apiKey = $this->config->get('openai.api_key');
        if (!$apiKey) {
            throw new ConfigurationException('OpenAI API key is not configured.');
        }

        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey,
        ];
        
        $messages = $history;
        $messages[] = ['role' => 'user', 'content' => $prompt];

        $body = [
            'model' => $this->config->get('openai.model', 'gpt-3.5-turbo'),
            'messages' => $messages,
        ];

        try {
            $this->logger->info('Sending request to OpenAI API.');
            $response = $this->httpClient->post(self::API_URL, $body, $headers);
            $this->logger->info('Received successful response from OpenAI API.');
        } catch (\Exception $e) {
            $this->logger->error('OpenAI API request failed.', ['exception' => $e]);
            throw new ProviderException('OpenAI API request failed: ' . $e->getMessage(), 0, $e);
        }

        $responseText = $response['choices'][0]['message']['content'] ?? '';
        $metadata = ['usage' => $response['usage'] ?? []];

        return new AiResponse($responseText, $metadata);
    }
}