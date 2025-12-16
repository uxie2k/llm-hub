<?php

namespace LLMHub\Config;

/**
 * Класс для управления конфигурацией.
 * Реализует паттерн "Data Transfer Object" (DTO) или "Value Object".
 * Он неизменяем (immutable), что повышает надежность системы.
 */
final class Config
{
    public function __construct(private readonly array $settings)
    {
    }

    /**
     * @param string $key Ключ в формате 'section.key'.
     * @param mixed|null $default Значение по умолчанию.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $keys = explode('.', $key);
        $value = $this->settings;
        foreach ($keys as $k) {
            if (!is_array($value) || !array_key_exists($k, $value)) {
                return $default;
            }
            $value = $value[$k];
        }
        return $value;
    }
}