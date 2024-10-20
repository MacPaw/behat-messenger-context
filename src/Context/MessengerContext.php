<?php

declare(strict_types=1);

namespace BehatMessengerContext\Context;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Hook\AfterFeature;
use Behat\Hook\AfterScenario;
use Behat\Hook\BeforeFeature;
use Behat\Hook\BeforeScenario;
use Exception;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Messenger\Transport\InMemory\InMemoryTransport;
use Symfony\Component\Messenger\Transport\Sync\SyncTransport;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Zenstruck\Messenger\Test\Bus\TestBus;
use Zenstruck\Messenger\Test\InteractsWithMessenger;
use Zenstruck\Messenger\Test\Transport\TestTransport;

class MessengerContext implements Context
{
    private ContainerInterface $container;
    private NormalizerInterface $normalizer;
    private TransportRetriever $transportRetriever;

    public function __construct(
        ContainerInterface $container,
        NormalizerInterface $normalizer,
        TransportRetriever $transportRetriever,
    ) {
        $this->container = $container;
        $this->normalizer = $normalizer;
        $this->transportRetriever = $transportRetriever;
    }

    #[BeforeFeature]
    public static function startTrackMessages(): void
    {
        if (class_exists(TestTransport::class)) {
            TestTransport::resetAll();
            TestTransport::enableMessagesCollection();
            TestTransport::disableResetOnKernelShutdown();
            TestBus::enableMessagesCollection();
        }
    }

    #[AfterFeature]
    public static function stopTrackMessages(): void
    {
        if (class_exists(TestTransport::class)) {
            TestTransport::resetAll();
        }
    }

    #[BeforeScenario]
    public function clearMessenger(): void
    {
        if (class_exists(TestTransport::class)) {
            TestTransport::resetAll();
        } else {
            $transports = $this->transportRetriever->getAllTransports();

            foreach ($transports as $transport) {
                if ($transport instanceof InMemoryTransport) {
                    $transport->reset();
                }
            }
        }
    }

    /**
     * @Then transport :transportName should contain message with JSON:
     */
    public function transportShouldContainMessageWithJson(string $transportName, PyStringNode $expectedMessage): void
    {
        $expectedMessage = $this->decodeExpectedJson($expectedMessage);

        $transport = $this->getMessengerTransportByName($transportName);
        $actualMessageList = [];
        foreach ($transport->get() as $envelope) {
            $actualMessage = $this->convertToArray($envelope->getMessage());
            if ($this->isMessagesAreSimilar($expectedMessage, $actualMessage)) {
                return;
            }

            $actualMessageList[] = $actualMessage;
        }

        throw new Exception(
            sprintf(
                'The transport doesn\'t contain message with such JSON, actual messages: %s',
                $this->getPrettyJson($actualMessageList),
            ),
        );
    }

    /**
     * @Then transport :transportName should contain message with JSON and variable fields :variableFields:
     */
    public function transportShouldContainMessageWithJsonAndVariableFields(
        string $transportName,
        string $variableFields,
        PyStringNode $expectedMessage,
    ): void {
        $variableFields = $variableFields ? array_map('trim', explode(',', $variableFields)) : [];
        $expectedMessage = $this->decodeExpectedJson($expectedMessage);

        $transport = $this->getMessengerTransportByName($transportName);
        $actualMessageList = [];
        foreach ($transport->get() as $envelope) {
            $actualMessage = $this->convertToArray($envelope->getMessage());
            if ($this->isMessagesAreSimilar($expectedMessage, $actualMessage, $variableFields)) {
                return;
            }

            $actualMessageList[] = $actualMessage;
        }

        throw new Exception(
            sprintf(
                'The transport doesn\'t contain message with such JSON, actual messages: %s',
                $this->getPrettyJson($actualMessageList),
            ),
        );
    }

    /**
     * @Then all transport :transportName messages should be JSON:
     */
    public function allTransportMessagesShouldBeJson(string $transportName, PyStringNode $expectedMessageList): void
    {
        $expectedMessageList = $this->decodeExpectedJson($expectedMessageList);

        $transport = $this->getMessengerTransportByName($transportName);
        $actualMessageList = [];
        foreach ($transport->get() as $envelope) {
            $actualMessageList[] = $this->convertToArray($envelope->getMessage());
        }

        if (!$this->isMessagesAreSimilar(
            expected: $expectedMessageList,
            actual: $actualMessageList,
            multipleActual: true,
        )) {
            throw new Exception(
                sprintf(
                    'The expected transport messages doesn\'t match actual: %s',
                    $this->getPrettyJson($actualMessageList),
                ),
            );
        }
    }

    /**
     * @Then all transport :transportName messages should be JSON with variable fields :variableFields:
     */
    public function allTransportMessagesShouldBeJsonWithVariableFields(
        string $transportName,
        string $variableFields,
        PyStringNode $expectedMessageList,
    ): void {
        $variableFields = $variableFields ? array_map('trim', explode(',', $variableFields)) : [];
        $expectedMessageList = $this->decodeExpectedJson($expectedMessageList);

        $transport = $this->getMessengerTransportByName($transportName);
        $actualMessageList = [];
        foreach ($transport->get() as $envelope) {
            $actualMessageList[] = $this->convertToArray($envelope->getMessage());
        }

        if (!$this->isMessagesAreSimilar(
            expected: $expectedMessageList,
            actual: $actualMessageList,
            requiredFields: $variableFields,
            multipleActual: true,
        )) {
            throw new Exception(
                sprintf(
                    'The expected transport messages doesn\'t match actual: %s',
                    $this->getPrettyJson($actualMessageList),
                ),
            );
        }
    }

    /**
     * @Then there is :expectationMessageCount messages in transport :transportName
     */
    public function thereIsCountMessagesInTransport(int $expectedMessageCount, string $transportName): void
    {
        $transport = $this->getMessengerTransportByName($transportName);
        $actualMessageCount = count($transport->get());

        if ($actualMessageCount !== $expectedMessageCount) {
            throw new Exception(
                sprintf(
                    'In transport exist actual count: %s, but expected count: %s',
                    $actualMessageCount,
                    $expectedMessageCount,
                ),
            );
        }
    }

    /**
     * @param array<mixed> $message
     *
     * @return string|bool
     */
    private function getPrettyJson(array $message)
    {
        return json_encode($message, JSON_PRETTY_PRINT);
    }

    /**
     * @param mixed $object
     *
     * @return array<mixed>
     */
    private function convertToArray($object): array
    {
        return (array)$this->normalizer->normalize($object);
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
            JSON_THROW_ON_ERROR,
        );
    }

    private function getMessengerTransportByName(string $transportName): InMemoryTransport
    {
        $fullName = 'messenger.transport.' . $transportName;
        $hasTransport = $this->container->has($fullName);

        if ($hasTransport === false) {
            throw new Exception('Transport ' . $fullName . ' not found');
        }

        $transport = $this->container->get($fullName);

        if ($transport instanceof InMemoryTransport) {
            return $transport;
        }

        throw new Exception(
            'In memory transport ' . $fullName . ' not found',
        );
    }

    /**
     * @param array $actual <mixed>
     * @param array $expected <mixed>
     * @param string[]|null $requiredFields
     *
     * @return bool
     */
    private function isMessagesAreSimilar(
        array $expected,
        array $actual,
        ?array $requiredFields = null,
        bool $multipleActual = false,
    ): bool {
        if ($multipleActual) {
            foreach ($actual as $nextActualItem) {
                if (!$this->isMessagesAreSimilar($expected, $nextActualItem, $requiredFields)) {
                    return false;
                }
            }

            return true;
        }

        $requiredFields = $requiredFields ?? array_keys($expected);

        foreach ($requiredFields as $requiredField) {
            if (!isset($actual[$requiredField])) {
                return false;
            }

            if (!isset($expected[$requiredField])) {
                return false;
            }

            if (is_string($expected[$requiredField]) && str_starts_with($expected[$requiredField], '~')) {
                $pregMatchValue = preg_match(
                    sprintf('|%s|', substr($expected[$requiredField], 1)),
                    sprintf('%s', $actual[$requiredField]),
                );

                return !($pregMatchValue === 0 || $pregMatchValue === false);
            }

            return $actual[$requiredField] === $expected[$requiredField];
        }

        return false;
    }
}
