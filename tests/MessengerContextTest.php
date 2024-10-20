<?php

declare(strict_types=1);

namespace BehatMessengerContext\Tests\Context;

use Behat\Gherkin\Node\PyStringNode;
use BehatMessengerContext\Context\MessengerContext;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\InMemory\InMemoryTransport;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use BehatMessengerContext\Context\TransportRetriever;
use Exception;

class MessengerContextTest extends TestCase
{
    private MessengerContext $messengerContext;

    /** @var ContainerInterface&MockObject */
    private ContainerInterface $container;

    /** @var NormalizerInterface&MockObject */
    private NormalizerInterface $normalizer;

    /** @var TransportRetriever&MockObject */
    private TransportRetriever $transportRetriever;

    /** @var InMemoryTransport&MockObject */
    private InMemoryTransport $inMemoryTransport;

    protected function setUp(): void
    {
        $this->container = $this->createMock(ContainerInterface::class);
        $this->normalizer = $this->createMock(NormalizerInterface::class);
        $this->transportRetriever = $this->createMock(TransportRetriever::class);
        $this->inMemoryTransport = $this->createMock(InMemoryTransport::class);

        $this->messengerContext = new MessengerContext(
            $this->container,
            $this->normalizer,
            $this->transportRetriever
        );
    }

    public function testClearMessenger(): void
    {
        $this->transportRetriever
            ->expects($this->once())
            ->method('getAllTransports')
            ->willReturn([$this->inMemoryTransport]);

        $this->inMemoryTransport
            ->expects($this->once())
            ->method('reset');

        $this->messengerContext->clearMessenger();
    }

    public function testTransportShouldContainMessageWithJson(): void
    {
        $message = new \stdClass();
        $expectedMessage = ['key' => 'value'];

        $this->inMemoryTransport
            ->expects($this->once())
            ->method('get')
            ->willReturn([new \Symfony\Component\Messenger\Envelope($message)]);

        $this->container
            ->expects($this->once())
            ->method('has')
            ->with('messenger.transport.test')
            ->willReturn(true);

        $this->container
            ->expects($this->once())
            ->method('get')
            ->with('messenger.transport.test')
            ->willReturn($this->inMemoryTransport);

        $this->normalizer
            ->expects($this->once())
            ->method('normalize')
            ->with($message)
            ->willReturn($expectedMessage);

        $this->messengerContext->transportShouldContainMessageWithJson(
            'test',
            new PyStringNode(['{ "key": "value" }'], 1)
        );
    }

    public function testTransportShouldContainMessageWithJsonThrowsException(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("The transport doesn't contain message with such JSON");

        $this->inMemoryTransport
            ->expects($this->once())
            ->method('get')
            ->willReturn([]);

        $this->container
            ->expects($this->once())
            ->method('has')
            ->with('messenger.transport.test')
            ->willReturn(true);

        $this->container
            ->expects($this->once())
            ->method('get')
            ->with('messenger.transport.test')
            ->willReturn($this->inMemoryTransport);

        $this->messengerContext->transportShouldContainMessageWithJson(
            'test',
            new PyStringNode(['{ "key": "value" }'], 1)
        );
    }

    public function testThereIsCountMessagesInTransport(): void
    {
        $this->inMemoryTransport
            ->expects($this->once())
            ->method('get')
            ->willReturn([1, 2, 3]);

        $this->container
            ->expects($this->once())
            ->method('has')
            ->with('messenger.transport.test')
            ->willReturn(true);

        $this->container
            ->expects($this->once())
            ->method('get')
            ->with('messenger.transport.test')
            ->willReturn($this->inMemoryTransport);

        $this->messengerContext->thereIsCountMessagesInTransport(3, 'test');
    }

    public function testThereIsCountMessagesInTransportThrowsException(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('In transport exist actual count: 2, but expected count: 3');

        $this->inMemoryTransport
            ->expects($this->once())
            ->method('get')
            ->willReturn([1, 2]);

        $this->container
            ->expects($this->once())
            ->method('has')
            ->with('messenger.transport.test')
            ->willReturn(true);

        $this->container
            ->expects($this->once())
            ->method('get')
            ->with('messenger.transport.test')
            ->willReturn($this->inMemoryTransport);

        $this->messengerContext->thereIsCountMessagesInTransport(3, 'test');
    }

    public function testFailTransportShouldContainMessageWithJsonAndVariableFields(): void
    {
        $message = new \stdClass();
        $message->id = '123';
        $message->name = 'TestMessage';
        $message->timestamp = '2024-10-17T12:00:00Z'; // Example variable field

        $expectedMessage = ['name' => 'TestMessage', 'timestamp' => 'IGNORED'];

        $this->inMemoryTransport
            ->expects($this->once())
            ->method('get')
            ->willReturn([new \Symfony\Component\Messenger\Envelope($message)]);

        $this->container
            ->expects($this->once())
            ->method('has')
            ->with('messenger.transport.test')
            ->willReturn(true);

        $this->container
            ->expects($this->once())
            ->method('get')
            ->with('messenger.transport.test')
            ->willReturn($this->inMemoryTransport);

        $this->normalizer
            ->expects($this->once())
            ->method('normalize')
            ->with($message)
            ->willReturn(['id' => '123', 'name' => 'TestMessage', 'timestamp' => '2024-10-17T12:00:00Z']);

        $this->expectException(Exception::class);
        $this->messengerContext->transportShouldContainMessageWithJsonAndVariableFields(
            'test',
            'timestamp',
            new PyStringNode(['{ "name": "TestMessage", "timestamp": "IGNORED" }'], 1)
        );
    }

    public function testTransportShouldContainMessageWithJsonAndVariableFields(): void
    {
        $message = new \stdClass();
        $message->id = 'unique';
        $message->name = 'TestMessage';
        $message->timestamp = '2024-10-17T12:00:00Z'; // Example variable field

        $expectedMessage = ['id' => 'unique', 'name' => 'TestMessage', 'timestamp' => 'IGNORED'];

        $this->inMemoryTransport
            ->expects($this->once())
            ->method('get')
            ->willReturn([new Envelope($message)]);

        $this->container
            ->expects($this->once())
            ->method('has')
            ->with('messenger.transport.test')
            ->willReturn(true);

        $this->container
            ->expects($this->once())
            ->method('get')
            ->with('messenger.transport.test')
            ->willReturn($this->inMemoryTransport);

        $this->normalizer
            ->expects($this->once())
            ->method('normalize')
            ->with($message)
            ->willReturn($expectedMessage);

        $this->messengerContext->transportShouldContainMessageWithJsonAndVariableFields(
            'test',
            'id,name,timestamp',
            new PyStringNode([json_encode($expectedMessage)], 1)
        );
    }
}
