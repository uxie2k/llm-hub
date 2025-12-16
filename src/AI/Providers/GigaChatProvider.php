<?php

namespace LLMHub\AI\Providers;

use LLMHub\AI\Response\AiResponse;
use LLMHub\Exception\ConfigurationException;
use LLMHub\Exception\ProviderException;

/**
 * Класс-провайдер для работы с GigaChat API от Сбера.
 * Он наследует всю общую логику от AbstractProvider и реализует
 * специфичные для GigaChat методы аутентификации и отправки запросов.
 */
final class GigaChatProvider extends AbstractProvider
{
    // Эндпоинты API GigaChat
    private const AUTH_URL = 'https://ngw.devices.sberbank.ru:9443/api/v2/oauth';
    private const CHAT_URL = 'https://gigachat.devices.sberbank.ru/api/v1/chat/completions';

    /**
     * @var string|null Кэшированный токен доступа.
     */
    private ?string $accessToken = null;

    /**
     * Основной метод, реализующий контракт AiProviderInterface.
     */
    public function chat(string $prompt, array $history = []): AiResponse
    {
        // 1. Получаем токен доступа (он будет получен только при первом вызове)
        $token = $this->getAccessToken();

        // 2. Формируем заголовки, специфичные для GigaChat
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token,
        ];
        
        $messages = $history;
        $messages[] = ['role' => 'user', 'content' => $prompt];

        // 3. Формируем тело запроса
        $body = [
            'model' => $this->config->get('gigachat.model', 'GigaChat:latest'),
            'messages' => $messages,
        ];

        // 4. Отправляем запрос через HTTP-клиент
        try {
            $this->logger->info('Sending request to GigaChat API.');
            $response = $this->httpClient->post(self::CHAT_URL, $body, $headers);
            $this->logger->info('Received successful response from GigaChat API.');
        } catch (\Exception $e) {
            $this->logger->error('GigaChat API request failed.', ['exception' => $e]);
            throw new ProviderException('GigaChat API request failed: ' . $e->getMessage(), 0, $e);
        }

        // 5. Парсим ответ и возвращаем наш унифицированный AiResponse
        $responseText = $response['choices'][0]['message']['content'] ?? '';
        $metadata = ['usage' => $response['usage'] ?? []];

        return new AiResponse($responseText, $metadata);
    }

    /**
     * Внутренний метод для получения токена доступа.
     * Реализует специфичную для GigaChat логику OAuth2.
     *
     * @throws ConfigurationException|ProviderException
     */
    private function getAccessToken(): string
    {
        if ($this->accessToken !== null) {
            // В реальном проекте здесь нужно проверять время жизни токена
            return $this->accessToken;
        }

        $authKey = $this->config->get('gigachat.credentials');
        if (!$authKey) {
            throw new ConfigurationException('GigaChat credentials are not configured.');
        }

        $headers = [
            'Content-Type: application/x-www-form-urlencoded',
            'Accept: application/json',
            'RqUID: ' . uniqid(),
            'Authorization: Basic ' . $authKey,
        ];
        
        $body = ['scope' => 'GIGACHAT_API_PERS'];

        try {
            $this->logger->info('Requesting GigaChat access token.');
            // Важно: здесь используется другой HTTP-клиент, т.к. тело запроса не JSON
            // Для простоты мы адаптируем существующий, но в идеале HTTP-клиент должен поддерживать разные форматы
            $authResponse = $this->sendAuthRequest(self::AUTH_URL, 'scope=GIGACHAT_API_PERS', $headers);
            
            $this->accessToken = $authResponse['access_token'] ?? null;
            
            if (!$this->accessToken) {
                throw new ProviderException('Failed to retrieve GigaChat access token.');
            }
            
            $this->logger->info('Successfully received GigaChat access token.');
            return $this->accessToken;

        } catch (\Exception $e) {
            $this->logger->error('GigaChat auth failed.', ['exception' => $e]);
            throw new ProviderException('GigaChat authentication failed: ' . $e->getMessage(), 0, $e);
        }
    }
    
    /**
      * Вспомогательный метод для отправки auth-запроса в формате x-www-form-urlencoded
      */
    private function sendAuthRequest(string $url, string $body, array $headers): array
    {
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => implode("\r\n", $headers),
                'content' => $body,
                'ignore_errors' => true,
            ],
        ]);
        $response = file_get_contents($url, false, $context);
        return json_decode($response, true);
    }
}