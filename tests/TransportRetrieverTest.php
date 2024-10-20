<?php

declare(strict_types=1);

namespace BehatMessengerContext\Tests;

use BehatMessengerContext\Context\TransportRetriever;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Transport\InMemory\InMemoryTransport;
use Symfony\Contracts\Service\ServiceProviderInterface;

final class TransportRetrieverTest extends TestCase
{
    public function testGetTransportsSuccessfully(): void
    {
        $inMemoryTransport = $this->createMock(InMemoryTransport::class);
        $serviceProvider = $this->createMock(ServiceProviderInterface::class);
        $serviceProvider
            ->expects($this->once())
            ->method('getProvidedServices')
            ->willReturn(['messenger.transport.test']);

        $serviceProvider
            ->expects(self::once())
            ->method('get')
            ->with('messenger.transport.test')
            ->willReturn($inMemoryTransport);

        $transportRetriever = new TransportRetriever($serviceProvider);

        self::assertEquals(
            ['messenger.transport.test' => $inMemoryTransport],
            $transportRetriever->getAllTransports(),
        );
    }
}
