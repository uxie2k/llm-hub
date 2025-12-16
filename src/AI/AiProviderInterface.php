<?php

namespace LLMHub\AI;

use LLMHub\AI\Response\AiResponse;
use LLMHub\Exception\ProviderException;

/**
 * Контракт для всех AI-провайдеров.
 * Демонстрирует:
 * - Interface Segregation Principle: определяет минимально необходимый метод.
 * - Liskov Substitution Principle: любой класс, реализующий этот интерфейс,
 *   может быть подставлен в Bot без изменения его поведения.
 */
interface AiProviderInterface
{
    /**
     * Отправляет запрос к LLM.
     *
     * @param string $prompt Запрос пользователя.
     * @param array $history Контекст диалога.
     * @return AiResponse Унифицированный ответ.
     * @throws ProviderException В случае ошибки со стороны API провайдера.
     */
    public function chat(string $prompt, array $history = []): AiResponse;
}