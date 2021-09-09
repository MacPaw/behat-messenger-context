<?php

declare(strict_types=1);

namespace BehatMessengerContext\Tests\Context\Traits;

use BehatMessengerContext\Context\Traits\ArraySimilarTrait;
use PHPUnit\Framework\TestCase;

class ArraySimilarTraitTest extends TestCase
{
    use ArraySimilarTrait;

    public function testSuccess(): void
    {
        $result = $this->isArraysSimilar(['test'], ['test']);

        self::assertTrue($result);
    }

    public function testEmptyArraySuccess(): void
    {
        $result = $this->isArraysSimilar([], []);

        self::assertTrue($result);
    }

    public function testFail(): void
    {
        $result = $this->isArraysSimilar([], ['test']);

        self::assertFalse($result);
    }

    public function testVariableFieldsSuccess(): void
    {
        $validValues = ['this', 'test'];
        $actualValue = $validValues[\array_rand($validValues)];

        $result = $this->isArraysSimilar(
            ['a' => '~^t[a-z]', 'b' => 1],
            ['a' => $actualValue, 'b' => 1],
            ['a']
        );

        self::assertTrue($result);
    }

    /**
     * @dataProvider variableFieldsFailProvider
     */
    public function testVariableFieldsFail(array $expected, array $actual, array $variableFields): void
    {
        $result = $this->isArraysSimilar($expected, $actual, $variableFields);

        self::assertFalse($result);
    }

    public function variableFieldsFailProvider(): iterable
    {
        yield '#1: Value type different from string' => [
            ['a' => 1],
            ['a' => 2],
            ['a'],
        ];

        yield '#2: Value without ~ prefix' => [
            ['a' => 'foo'],
            ['a' => 'bar'],
            ['a'],
        ];
    }
}
