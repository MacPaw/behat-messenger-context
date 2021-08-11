<?php

declare(strict_types=1);

namespace MessengerBehatContext\Context;

use MessengerBehatContext\Context\Traits\ArraySimilarTrait;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Exception;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Messenger\Transport\InMemoryTransport;
use Symfony\Component\Serializer\Serializer;

class MessengerContext implements Context
{
    use ArraySimilarTrait;

    private ContainerInterface $container;
    private Serializer $serializer;

    public function __construct(
        ContainerInterface $container,
        Serializer $serializer
    ) {
        $this->container = $container;
        $this->serializer = $serializer;
    }

    /**
     * @Then bus :busName should contain message with JSON:
     */
    public function busShouldContainMessageWithJson(string $busName, PyStringNode $expectedMessage): void
    {
        $expectedMessage = $this->decodeExpectedJson($expectedMessage);

        $transport = $this->getMessengerTransportByName($busName);
        foreach ($transport->get() as $envelope) {
            $actualMessage = $this->convertToArray($envelope->getMessage());
            if ($this->isArraysSimilar($actualMessage, $expectedMessage)) {
                return;
            }
        }

        throw new Exception(
            sprintf(
                'The transport doesn\'t contain message with JSON: %s',
                $this->getPrettyJson($expectedMessage)
            )
        );
    }

    /**
     * @Then bus :busName should contain message with JSON and variable fields :variableFields:
     */
    public function busShouldContainMessageWithJsonAndVariableFields(
        string $busName,
        string $variableFields,
        PyStringNode $expectedMessage
    ): void {
        $variableFields = $variableFields ? array_map('trim', explode(',', $variableFields)) : [];
        $expectedMessage = $this->decodeExpectedJson($expectedMessage);

        $transport = $this->getMessengerTransportByName($busName);
        foreach ($transport->get() as $envelope) {
            $actualMessage = $this->convertToArray($envelope->getMessage());
            if ($this->isArraysSimilar($actualMessage, $expectedMessage, $variableFields)) {
                return;
            }
        }

        throw new Exception(
            sprintf(
                'The transport doesn\'t contain message with JSON: %s',
                $this->getPrettyJson($expectedMessage)
            )
        );
    }

    /**
     * @Then all bus :busName messages should be JSON:
     */
    public function allBusMessagesShouldBeJson(string $busName, PyStringNode $expectedMessageList): void
    {
        $expectedMessageList = $this->decodeExpectedJson($expectedMessageList);

        $transport = $this->getMessengerTransportByName($busName);
        $actualMessageList = [];
        foreach ($transport->get() as $envelope) {
            $actualMessageList[] = $this->convertToArray($envelope->getMessage());
        }

        if (!$this->isArraysSimilar($actualMessageList, $expectedMessageList)) {
            throw new Exception(
                sprintf(
                    'The expected bus messages doesn\'t match actual: %s',
                    $this->getPrettyJson($actualMessageList)
                )
            );
        }
    }

    /**
     * @Then all bus :busName messages should be JSON with variable fields :variableFields:
     */
    public function allBusMessagesShouldBeJsonWithVariableFields(
        string $busName,
        string $variableFields,
        PyStringNode $expectedMessageList
    ): void {
        $variableFields = $variableFields ? array_map('trim', explode(',', $variableFields)) : [];
        $expectedMessageList = $this->decodeExpectedJson($expectedMessageList);

        $transport = $this->getMessengerTransportByName($busName);
        $actualMessageList = [];
        foreach ($transport->get() as $envelope) {
            $actualMessageList[] = $this->convertToArray($envelope->getMessage());
        }

        if (!$this->isArraysSimilar($actualMessageList, $expectedMessageList, $variableFields)) {
            throw new Exception(
                sprintf(
                    'The expected bus messages doesn\'t match actual: %s',
                    $this->getPrettyJson($actualMessageList)
                )
            );
        }
    }

    /**
     * @Then there is :expectationMessageCount messages in bus :busName
     */
    public function thereIsCountMessagesInBus(int $expectedMessageCount, string $busName): void
    {
        $transport = $this->getMessengerTransportByName($busName);
        $actualMessageCount = count($transport->get());

        if ($actualMessageCount !== $expectedMessageCount) {
            throw new Exception(
                sprintf(
                    'In transport exist actual count: %s, but expected count: %s',
                    $actualMessageCount,
                    $expectedMessageCount
                )
            );
        }
    }

    /**
     * @param array<mixed> $message
     * @return string|bool
     */
    private function getPrettyJson(array $message)
    {
        return json_encode($message, JSON_PRETTY_PRINT);
    }

    /**
     * @param mixed $object
     * @return array<mixed>
     */
    private function convertToArray($object): array
    {
        return (array) $this->serializer->normalize($object);
    }

    /**
     * @return array<mixed>
     */
    private function decodeExpectedJson(PyStringNode $expectedJson): array
    {
        return json_decode(
            trim($expectedJson->getRaw()),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
    }

    private function getMessengerTransportByName(string $busName): InMemoryTransport
    {
        $fullName = 'messenger.transport.' . $busName;
        $hasTransport = $this->container->has($fullName);

        if ($hasTransport === false) {
            throw new Exception('Transport' . $fullName . ' not found');
        }

        $transport = $this->container->get($fullName);

        if ($transport instanceof InMemoryTransport) {
            return $transport;
        }

        throw new Exception(
            'In memory transport' . $fullName . ' not found'
        );
    }
}
