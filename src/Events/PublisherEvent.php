<?php

namespace Evaneos\Hector\Events;

use Evaneos\Hector\Exchange\Exchange;
use Symfony\Component\EventDispatcher\Event;

class PublisherEvent extends Event implements PublisherEventInterface
{
    use PublisherEventTrait;

    /**
     * PublisherEvent constructor.
     *
     * @param          $message
     * @param null     $routingKey
     * @param int      $flags
     * @param array    $attributes
     * @param Exchange $exchange
     */
    public function __construct($message, $routingKey = null, $flags = AMQP_NOPARAM, array $attributes = [], Exchange $exchange)
    {
        $this->message    = $message;
        $this->routingKey = $routingKey;
        $this->flags      = $flags;
        $this->attributes = $attributes;
        $this->exchange   = $exchange;
    }
}
