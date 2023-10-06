<?php

namespace Bazinga\Bundle\PropelEventDispatcherBundle\Tests\Fixtures\EventListener;

use Symfony\Contracts\EventDispatcher\Event;

class MyEventListener
{
    private array $events = [];

    public function preSave(Event $event): void
    {
        $this->events[] = $event;
    }

    public function getEvents(): array
    {
        return $this->events;
    }
}
