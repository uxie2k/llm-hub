<?php

namespace LLMHub\AI\Response;

/**
 * DTO для унифицированного ответа от AI. Неизменяемый.
 */
final class AiResponse
{
    public function __construct(
        public readonly string $text,
        public readonly array $metadata = []
    ) {}
}