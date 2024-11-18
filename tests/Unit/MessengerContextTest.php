<?php

declare(strict_types=1);

namespace BehatMessengerContext\Tests\Unit;

use Behat\Gherkin\Node\PyStringNode;
use BehatMessengerContext\Context\MessengerContext;
use BehatMessengerContext\Context\TransportRetriever;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\InMemory\InMemoryTransport;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Contracts\Service\ServiceProviderInterface;

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
        $this->container = $this->createMock(Container::class);
        $this->normalizer = $this->createMock(NormalizerInterface::class);
        $this->transportRetriever = $this->createMock(TransportRetriever::class);
        $this->inMemoryTransport = $this->createMock(InMemoryTransport::class);

        $this->messengerContext = new MessengerContext(
            $this->container,
            $this->normalizer,
            $this->transportRetriever
        );
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

    public function testFailTransportShouldContainMessageWithJson(): void
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

        $this->expectException(Exception::class);
        $this->messengerContext->transportShouldContainMessageWithJson(
            'test',
            new PyStringNode(['{ "key1": "value" }'], 1)
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
            '',
            new PyStringNode([json_encode($expectedMessage)], 1)
        );
    }

    public function testTransportNotFoundThrowsException(): void
    {
        $this->container->method('has')->willReturn(false);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Transport messenger.transport.invalid not found');

        $this->messengerContext->transportShouldContainMessageWithJson(
            'invalid',
            new PyStringNode([json_encode([])], 1),
        );
    }

    public function testAllTransportMessagesShouldBeJson(): void
    {
        $message = new \stdClass();
        $message->key = 'value';

        $envelope = new Envelope($message);

        $transport = $this->createMock(InMemoryTransport::class);
        $transport->method('get')->willReturn([$envelope]);

        $this->container
            ->expects(self::once())
            ->method('has')
            ->with('messenger.transport.test')
            ->willReturn(true);

        $this->container
            ->expects(self::once())
            ->method('get')
            ->with('messenger.transport.test')
            ->willReturn($transport);

        $this->normalizer
            ->method('normalize')
            ->with($message)
            ->willReturn(['key' => 'value']);

        $expectedJson = new PyStringNode(['[{"key": "value"}]'], 1);

        $this->messengerContext->allTransportMessagesShouldBeJson('test', $expectedJson);
    }

    public function testFailAllTransportMessagesShouldBeJson(): void
    {
        $message = new \stdClass();
        $message->key = 'value';

        $envelope = new Envelope($message);

        $transport = $this->createMock(InMemoryTransport::class);
        $transport->method('get')->willReturn([$envelope]);

        $this->container
            ->expects(self::once())
            ->method('has')
            ->with('messenger.transport.test')
            ->willReturn(true);

        $this->container
            ->expects(self::once())
            ->method('get')
            ->with('messenger.transport.test')
            ->willReturn($transport);

        $this->normalizer
            ->method('normalize')
            ->with($message)
            ->willReturn(['key' => 'value']);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('The expected transport messages doesn\'t match actual');
        $expectedJson = new PyStringNode(['[{"key1": "value"}]'], 1);

        $this->messengerContext->allTransportMessagesShouldBeJson('test', $expectedJson);
    }

    public function testAllTransportMessagesShouldBeJsonWithVariableFields(): void
    {
        $message1 = new \stdClass();
        $message1->id = 1;
        $message1->name = 'Test';

        $message2 = new \stdClass();
        $message2->id = 2;
        $message2->name = 'Test';

        $message3 = new \stdClass();
        $message3->id = 1000;
        $message3->name = 'Test5';

        $envelope1 = new Envelope($message1);
        $envelope2 = new Envelope($message2);
        $envelope3 = new Envelope($message3);

        $transport = $this->createMock(InMemoryTransport::class);
        $transport->method('get')->willReturn([$envelope1, $envelope2, $envelope3]);

        $this->container
            ->expects(self::once())
            ->method('has')
            ->with('messenger.transport.test')
            ->willReturn(true);
        $this->container
            ->expects(self::once())
            ->method('get')
            ->with('messenger.transport.test')
            ->willReturn($transport);

        $this->normalizer
            ->expects($this->exactly(3))
            ->method('normalize')
            ->willReturnOnConsecutiveCalls(
                ['id' => 1, 'name' => 'Test'],
                ['id' => 2, 'name' => 'Test'],
                ['id' => 1000, 'name' => 'Test5'],
            );

        $expectedJson = new PyStringNode([
            '[{"id": "~[0-9]+", "name": "Test"}, {"id": "~[0-9]+", "name": "Test"}, {"id": "~[0-9]+", "name": "Test5"}]'
        ], 1);

        $this->messengerContext->allTransportMessagesShouldBeJsonWithVariableFields(
            'test',
            'id',
            $expectedJson
        );
    }

    public function testFailAllTransportMessagesShouldBeJsonWithVariableFields(): void
    {
        $message1 = new \stdClass();
        $message1->id = 1;
        $message1->name = 'Test';

        $message2 = new \stdClass();
        $message2->id = 2;
        $message2->name = 'Test';

        $envelope1 = new Envelope($message1);
        $envelope2 = new Envelope($message2);

        $transport = $this->createMock(InMemoryTransport::class);
        $transport->method('get')->willReturn([$envelope1, $envelope2]);

        $this->container
            ->expects(self::once())
            ->method('has')
            ->with('messenger.transport.test')
            ->willReturn(true);
        $this->container
            ->expects(self::once())
            ->method('get')
            ->with('messenger.transport.test')
            ->willReturn($transport);

        $this->normalizer
            ->expects($this->exactly(2))
            ->method('normalize')
            ->willReturnOnConsecutiveCalls(
                ['id' => 1, 'name' => 'Test'],
                ['id' => 'uuid', 'name' => 'Test']
            );

        $expectedJson = new PyStringNode([
            '{"id": "~\\\\d+", "name": "Test"}'
        ], 1);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('The expected transport messages doesn\'t match actual');
        $this->messengerContext->allTransportMessagesShouldBeJsonWithVariableFields(
            'test',
            'id',
            $expectedJson
        );
    }

    public function testTransportWasReset(): void
    {
        $serviceProvider = $this->createMock(ServiceProviderInterface::class);
        $serviceProvider
            ->expects($this->once())
            ->method('getProvidedServices')
            ->willReturn(['messenger.transport.test']);

        $serviceProvider
            ->expects(self::once())
            ->method('get')
            ->with('messenger.transport.test')
            ->willReturn($this->inMemoryTransport);

        $this->inMemoryTransport
            ->expects($this->once())
            ->method('reset');

        (new MessengerContext(
            $this->container,
            $this->normalizer,
            new TransportRetriever($serviceProvider)
        ))->clearMessengerBeforeScenario();
    }

    public function testTransportWasResetWithZentruck(): void
    {
        require_once __DIR__ . '/../Stub/Zentruck/TestBus.php';
        require_once __DIR__ . '/../Stub/Zentruck/TestTransport.php';

        $transportClass = 'Zenstruck\Messenger\Test\Transport\TestTransport';

        $transportClass::reset();
        $this->messengerContext::stopTrackMessages();

        $this->assertEquals(
            $transportClass::RESET_ALL,
            $transportClass::getResult() & $transportClass::RESET_ALL
        );

        $this->assertNotEquals(
            $transportClass::DISABLE_RESET_ON_KERNEL_SHUTDOWN,
            $transportClass::getResult() & $transportClass::DISABLE_RESET_ON_KERNEL_SHUTDOWN
        );

        $this->assertNotEquals(
            $transportClass::ENABLE_MESSAGES_COLLECTION,
            $transportClass::getResult() & $transportClass::ENABLE_MESSAGES_COLLECTION
        );
    }

    public function testClearWithZentruck(): void
    {
        require_once __DIR__ . '/../Stub/Zentruck/TestBus.php';
        require_once __DIR__ . '/../Stub/Zentruck/TestTransport.php';

        $transportClass = 'Zenstruck\Messenger\Test\Transport\TestTransport';
        $busClass = 'Zenstruck\Messenger\Test\Bus\TestBus';

        $transportClass::reset();
        $busClass::reset();
        $this->messengerContext::startTrackMessages();

        $this->assertEquals(
            $transportClass::RESET_ALL,
            $transportClass::getResult() & $transportClass::RESET_ALL
        );

        $this->assertEquals(
            $transportClass::DISABLE_RESET_ON_KERNEL_SHUTDOWN,
            $transportClass::getResult() & $transportClass::DISABLE_RESET_ON_KERNEL_SHUTDOWN
        );

        $this->assertEquals(
            $transportClass::ENABLE_MESSAGES_COLLECTION,
            $transportClass::getResult() & $transportClass::ENABLE_MESSAGES_COLLECTION
        );

        $this->assertEquals(
            $busClass::ENABLE_MESSAGES_COLLECTION,
            $busClass::getResult() & $busClass::ENABLE_MESSAGES_COLLECTION
        );
    }
}
