<?php

namespace Evaneos\Hector\Events;

use Evaneos\Hector\Publisher\Publisher;
use Symfony\Component\EventDispatcher\Event;

class FailedPublisherEvent extends Event implements PublisherEventInterface
{
    /** @var PublisherEvent  */
    private $event;

    /** @var  \Exception */
    private $exception;

    /** @var  Publisher */
    private $publisher;

    use PublisherEventTrait;

    /**
     * FailedPublisherEvent constructor.
     *
     * @param PublisherEventInterface $event
     * @param \Exception              $exception
     * @param Publisher               $publisher
     */
    public function __construct(PublisherEventInterface $event, \Exception $exception = null, Publisher $publisher)
    {
        $this->event      = $event;
        $this->catched    = false;
        $this->message    = $event->getMessage();
        $this->routingKey = $event->getRoutingKey();
        $this->attributes = $event->getAttributes();
        $this->flags      = $event->getFlags();
        $this->exception  = $exception;
        $this->publisher  = $publisher;
    }

    /**
     * @return bool
     */
    public function hasException()
    {
        return null !== $this->exception;
    }

    /**
     * @return \Exception
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * @return Publisher
     */
    public function getPublisher()
    {
        return $this->publisher;
    }
}
