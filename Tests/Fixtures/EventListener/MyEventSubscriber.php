<?php

namespace Bazinga\Bundle\PropelEventDispatcherBundle\Tests\Fixtures\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class MyEventSubscriber implements EventSubscriberInterface
{
    private array $events = [];

    public function preInsert(GenericEvent $event): void
    {
        $subject = $event->getSubject();
        $subject->source = 'pre_insert';

        $this->events[] = $event;
    }

    public function getEvents(): array
    {
        return $this->events;
    }

    public static function getSubscribedEvents(): array
    {
        return array(
            'propel.pre_insert' => 'preInsert',
        );
    }
}
