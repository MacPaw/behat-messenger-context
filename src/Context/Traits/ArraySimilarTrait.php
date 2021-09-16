<?php

declare(strict_types=1);

namespace BehatMessengerContext\Context\Traits;

trait ArraySimilarTrait
{
    /**
     * @param array<mixed>  $expected
     * @param array<mixed>  $actual
     * @param array<string> $variableFields
     * @param array<string, string> $placeholderPatternMap
     */
    protected function isArraysSimilar(
        array $expected,
        array $actual,
        array $variableFields = [],
        array $placeholderPatternMap = []
    ): bool {
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
                if (!$this->isArraysSimilar($value, $actual[$key], $variableFields, $placeholderPatternMap)) {
                    return false;
                }
            } elseif (!in_array($key, $variableFields, true) && ($actual[$key] !== $value)) {
                return false;
            } elseif (in_array($key, $variableFields, true)) {
                if (!is_string($value)) {
                    return false;
                }

                $isPlaceholder = !empty($placeholderPatternMap)
                    && strpos($value, '{') === 0
                    && \substr($value, -1) === '}';

                if (strpos($value, '~') !== 0 && !$isPlaceholder) {
                    return false;
                }

                $pattern = sprintf('|%s|', substr($value, 1));

                if ($isPlaceholder) {
                    $placeholder = \str_replace(['{', '}'], '', $value);
                    $pattern = $placeholderPatternMap[$placeholder];
                }

                if (!preg_match($pattern, $actual[$key])) {
                    return false;
                }
            }
        }

        return true;
    }
}
