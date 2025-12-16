<?php

namespace LLMHub\Exception;

/**
 * Базовый интерфейс для всех исключений библиотеки.
 * Позволяет ловить любое исключение из нашего кода через catch(LLMHubException $e).
 */
interface LLMHubException extends \Throwable
{
}