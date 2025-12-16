<?php

namespace LLMHub\Http;

use LLMHub\Exception\HttpException;

/**
 * Надежный HTTP-клиент, основанный на cURL.
 * Реализует ClientInterface и умеет управлять проверкой SSL через опции.
 */
final class CurlClient implements ClientInterface
{
    private array $options;

    /**
     * @param array $options Опции для клиента, например ['ssl_verify' => false]
     */
    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    public function post(string $url, array|string $body, array $headers): array
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, is_array($body) ? json_encode($body) : $body);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        // Управляем проверкой SSL через опции, а не жестко в коде
        if (isset($this->options['ssl_verify']) && $this->options['ssl_verify'] === false) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new HttpException("cURL Error: " . $error);
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 400) {
            throw new HttpException("API request failed with status {$httpCode}: {$response}", $httpCode);
        }
        
        $decodedResponse = json_decode($response, true);

        if ($response && json_last_error() !== JSON_ERROR_NONE) {
            throw new HttpException("Failed to decode JSON response. Raw response: " . $response);
        }
        
        return $decodedResponse ?? [];
    }
}