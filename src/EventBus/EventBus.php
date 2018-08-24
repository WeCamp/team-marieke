<?php declare(strict_types=1);

namespace CorrectHorseBattery\EventBus;

use Psr\Log\LoggerInterface;

final class EventBus
{
    private $eventSubscribers = [];
    private $log;

    public function __construct(LoggerInterface $log)
    {
        $this->log = $log;
    }

    public function dispatch(object $event)
    {
        $this->log->debug('Dispatching event.', ['event_name' => get_class($event)]);
        if (array_key_exists(get_class($event), $this->eventSubscribers)) {
            $this->eventSubscribers[get_class($event)]($event);
        }
    }

    public function subscribe(string $eventName, callable $subscriber)
    {
        $this->eventSubscribers[$eventName] = $subscriber;
    }
}
