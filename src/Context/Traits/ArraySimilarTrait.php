<?php

declare(strict_types=1);

namespace BehatMessengerContext\Context\Traits;

trait ArraySimilarTrait
{
    /**
     * @param array<mixed>  $expected
     * @param array<mixed>  $actual
     * @param array<string> $variableFields
     */
    protected function isArraysSimilar(array $expected, array $actual, array $variableFields = []): bool
    {
        if (array_keys($expected) !== array_keys($actual)) {
            return false;
        }

        foreach ($expected as $key => $value) {
            if (!isset($actual[$key]) && $value !== null) {
                return false;
            }

            if (gettype($value) !== gettype($actual[$key]) && !in_array($key, $variableFields)) {
                return false;
            }

            if (is_array($value)) {
                if (!$this->isArraysSimilar($value, $actual[$key], $variableFields)) {
                    return false;
                }
            } elseif (!in_array($key, $variableFields, true) && ($actual[$key] !== $value)) {
                return false;
            } elseif (in_array($key, $variableFields, true)) {
                if (
                    is_string($value) && strpos($value, '~') === 0
                    && !preg_match(sprintf('|%s|', substr($value, 1)), $actual[$key])
                ) {
                    return false;
                }
            }
        }

        return true;
    }
}
