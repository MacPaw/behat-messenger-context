<?php

declare(strict_types=1);

namespace Zenstruck\Messenger\Test\Transport;

class TestTransport
{
    public const RESET_ALL = 0x16;
    public const ENABLE_MESSAGES_COLLECTION = 0x256;
    public const DISABLE_RESET_ON_KERNEL_SHUTDOWN = 0x1024;
    private static int $callStacks = 0x1;

    public static function resetAll(): void
    {
        self::$callStacks = self::$callStacks | self::RESET_ALL;
    }

    public static function enableMessagesCollection(): void
    {
        self::$callStacks = self::$callStacks | self::ENABLE_MESSAGES_COLLECTION;
    }

    public static function disableResetOnKernelShutdown(): void
    {
        self::$callStacks = self::$callStacks | self::DISABLE_RESET_ON_KERNEL_SHUTDOWN;
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

