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
}
