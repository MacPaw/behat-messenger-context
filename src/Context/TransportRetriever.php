<?php

declare(strict_types=1);

namespace BehatMessengerContext\Context;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;
use Symfony\Contracts\Service\ServiceProviderInterface;

class TransportRetriever
{
    private ServiceProviderInterface $receiverLocator;

    public function __construct(
        ServiceProviderInterface $receiverLocator,
    ) {
        $this->receiverLocator = $receiverLocator;
    }

    /**
     * @return TransportInterface[]
     */
    public function getAllTransports(): array
    {
        $transports = [];

        foreach ($this->receiverLocator->getProvidedServices() as $name => $service) {
            $transports[$name] = $this->receiverLocator->get($name);
        }

        return $transports;
    }
}
