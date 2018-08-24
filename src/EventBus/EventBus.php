<?php declare(strict_types=1);

namespace CorrectHorseBattery\EventBus;

final class EventBus
{
    private $eventSubscribers = [];

    public function dispatch(object $event)
    {
        if (array_key_exists(get_class($event), $this->eventSubscribers)) {
            $this->eventSubscribers[get_class($event)]($event);
        }
    }

    public function subscribe(string $eventName, callable $subscriber)
    {
        $this->eventSubscribers[$eventName] = $subscriber;
    }
}
