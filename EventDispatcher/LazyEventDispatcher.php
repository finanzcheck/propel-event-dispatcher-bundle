<?php

namespace Bazinga\Bundle\PropelEventDispatcherBundle\EventDispatcher;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LazyEventDispatcher implements EventDispatcherInterface
{
    private ContainerInterface $container;
    private string $serviceId;
    private ?EventDispatcherInterface $eventDispatcher = null;

    public function __construct(ContainerInterface $container, string $serviceId)
    {
        $this->container = $container;
        $this->serviceId = $serviceId;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch($event, $eventName = null): object
    {
        return $this->getEventDispatcher()->dispatch($event, $eventName);
    }

    /**
     * {@inheritdoc}
     */
    public function addListener($eventName, $listener, $priority = 0): void
    {
        $this->getEventDispatcher()->addListener($eventName, $listener, $priority);
    }

    /**
     * {@inheritdoc}
     */
    public function addSubscriber(EventSubscriberInterface $subscriber): void
    {
        $this->getEventDispatcher()->addSubscriber($subscriber);
    }

    /**
     * {@inheritdoc}
     */
    public function removeListener($eventName, $listener): void
    {
        $this->getEventDispatcher()->removeListener($eventName, $listener);
    }

    /**
     * {@inheritdoc}
     */
    public function removeSubscriber(EventSubscriberInterface $subscriber): void
    {
        $this->getEventDispatcher()->removeSubscriber($subscriber);
    }

    /**
     * {@inheritdoc}
     */
    public function getListeners($eventName = null): array
    {
        return $this->getEventDispatcher()->getListeners($eventName);
    }

    /**
     * {@inheritdoc}
     */
    public function getListenerPriority($eventName, $listener): ?int
    {
        return $this->getEventDispatcher()->getListenerPriority($eventName, $listener);
    }

    /**
     * {@inheritdoc}
     */
    public function hasListeners($eventName = null): bool
    {
        return $this->getEventDispatcher()->hasListeners($eventName);
    }

    protected function getEventDispatcher(): EventDispatcherInterface
    {
        if (null === $this->eventDispatcher) {
            $this->eventDispatcher = $this->container->get($this->serviceId);
        }

        return $this->eventDispatcher;
    }
}
