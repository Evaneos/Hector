<?php

namespace Evaneos\Hector\Events;

use Evaneos\Hector\Exchange\Exchange;

interface PublisherEventInterface
{
    /**
     * @return mixed
     */
    public function getMessage();

    /**
     */
    public function getRoutingKey();

    /**
     * @return int
     */
    public function getFlags();

    /**
     * @return array
     */
    public function getAttributes();

    /**
     * @return Exchange
     */
    public function getExchange();

    /**
     * @param mixed $message
     */
    public function setMessage($message);

    /**
     * @param null $routingKey
     */
    public function setRoutingKey($routingKey);

    /**
     * @param int $flags
     */
    public function setFlags($flags);

    /**
     * @param array $attributes
     */
    public function setAttributes($attributes);

    /**
     * @param Exchange $exchange
     */
    public function setExchange($exchange);
}
