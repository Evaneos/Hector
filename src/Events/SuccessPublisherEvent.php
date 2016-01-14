<?php

namespace Evaneos\Hector\Events;

use Symfony\Component\EventDispatcher\Event;

class SuccessPublisherEvent extends Event implements PublisherEventInterface
{
    use PublisherEventTrait;

    /**
     * SuccessPublisherEvent constructor.
     *
     * @param PublisherEventInterface $event
     */
    public function __construct(PublisherEventInterface $event)
    {
        $this->message    = $event->getMessage();
        $this->routingKey = $event->getRoutingKey();
        $this->attributes = $event->getAttributes();
        $this->flags      = $event->getFlags();
    }
}
