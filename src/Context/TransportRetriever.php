<?php

declare(strict_types=1);

namespace BehatMessengerContext\Context;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;

class TransportRetriever
{
    /** @var ContainerInterface */
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return TransportInterface[]
     */
    public function getAllTransports(): array
    {
        $transports = [];
        assert($this->container instanceof Container);

        foreach ($this->container->getServiceIds() as $serviceId) {
            if (
                str_starts_with($serviceId, 'messenger.transport.') &&
                $this->container->has($serviceId)
            ) {
                $transports[$serviceId] = $this->container->get($serviceId);
            }
        }
        return $transports;
    }
}
