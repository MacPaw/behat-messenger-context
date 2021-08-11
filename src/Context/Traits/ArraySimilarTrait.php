<?php

declare(strict_types=1);

namespace MessengerBehatContext\Context\Traits;

trait ArraySimilarTrait
{
    /**
     * @param array<mixed> $expected
     * @param array<mixed> $actual
     * @param array<string> $variableFields
     */
    protected function isArraysSimilar(array $expected, array $actual, array $variableFields = []): bool
    {
        if (array_keys($expected) !== array_keys($actual)) {
            return false;
        }

        foreach ($expected as $k => $v) {
            if (!isset($actual[$k]) && $v !== null) {
                return false;
            }

            if (gettype($expected[$k]) !== gettype($actual[$k]) && !in_array($k, $variableFields)) {
                return false;
            }

            if (is_array($v)) {
                if (!$this->isArraysSimilar($expected[$k], $actual[$k], $variableFields)) {
                    return false;
                }
            } elseif (!in_array($k, $variableFields, true) && ($expected[$k] !== $actual[$k])) {
                return false;
            } elseif (in_array($k, $variableFields, true)) {
                if (
                    is_string($expected[$k]) && strpos($expected[$k], '~') === 0
                    && !preg_match(sprintf('|%s|', substr($expected[$k], 1)), $actual[$k])
                ) {
                    return false;
                }
            }
        }

        return true;
    }
}
