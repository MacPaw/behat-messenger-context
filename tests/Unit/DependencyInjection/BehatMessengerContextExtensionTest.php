<?php

declare(strict_types=1);

namespace BehatMessengerContext\Tests\Unit\DependencyInjection;

use BehatMessengerContext\Context\MessengerContext;
use BehatMessengerContext\DependencyInjection\BehatMessengerContextExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

class BehatMessengerContextExtensionTest extends TestCase
{
    public function testHasServices(): void
    {
        $extension = new BehatMessengerContextExtension();
        $container = new ContainerBuilder();

        $this->assertInstanceOf(Extension::class, $extension);

        $extension->load([], $container);

        $this->assertTrue($container->has(MessengerContext::class));
    }
}
