<?php

declare(strict_types=1);

namespace Zenstruck\Messenger\Test\Bus;

class TestBus
{
    public const ENABLE_MESSAGES_COLLECTION = 0x256;

    private static int $callStacks = 0x1;

    public static function enableMessagesCollection(): void
    {
        self::$callStacks = self::$callStacks | self::ENABLE_MESSAGES_COLLECTION;
    }

    public static function getResult(): int
    {
        return self::$callStacks;
    }

    public static function reset(): void
    {
        self::$callStacks = 0x1;
    }
}
