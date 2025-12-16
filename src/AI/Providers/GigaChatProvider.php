<?php

namespace LLMHub\AI\Providers;

use LLMHub\AI\Response\AiResponse;
use LLMHub\Exception\ConfigurationException;
use LLMHub\Exception\ProviderException;

final class GigaChatProvider extends AbstractProvider
{
    private const AUTH_URL = 'https://ngw.devices.sberbank.ru:9443/api/v2/oauth';
    private const CHAT_URL = 'https://gigachat.devices.sberbank.ru/api/v1/chat/completions';
    private ?string $accessToken = null;

    public function chat(string $prompt, array $history = []): AiResponse
    {
        $token = $this->getAccessToken();
        $headers = ['Content-Type: application/json', 'Authorization: Bearer ' . $token];
        $messages = $history;
        $messages[] = ['role' => 'user', 'content' => $prompt];
        $body = [
            'model' => $this->config->get('gigachat.model', 'GigaChat:latest'),
            'messages' => $messages,
        ];

        $response = $this->httpClient->post(self::CHAT_URL, $body, $headers);
        
        $responseText = $response['choices'][0]['message']['content'] ?? 'Не удалось получить ответ от GigaChat.';
        $metadata = ['usage' => $response['usage'] ?? []];

        return new AiResponse($responseText, $metadata);
    }

    private function getAccessToken(): string
    {
        if ($this->accessToken !== null) {
            return $this->accessToken;
        }

        $authKey = $this->config->get('gigachat.credentials');
        if (!$authKey) {
            throw new ConfigurationException('GigaChat credentials are not configured.');
        }
        
        $rqUid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000, mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));
        $headers = ['Content-Type: application/x-www-form-urlencoded', 'Accept: application/json', 'RqUID: ' . $rqUid, 'Authorization: Basic ' . trim($authKey)];
        $body = 'scope=GIGACHAT_API_PERS';
        
        try {
            // Провайдер просто использует httpClient, не зная, как он работает. Это и есть SOLID!
            $authResponse = $this->httpClient->post(self::AUTH_URL, $body, $headers);

            $this->accessToken = $authResponse['access_token'] ?? null;
            if (!$this->accessToken) {
                throw new ProviderException('Failed to retrieve GigaChat access token. Response: ' . json_encode($authResponse));
            }
            return $this->accessToken;
        } catch (\Exception $e) {
            throw new ProviderException('GigaChat authentication failed: ' . $e->getMessage(), 0, $e);
        }
    }
}