<?php

namespace Evaneos\Hector\Events;

use Evaneos\Hector\Exchange\Exchange;

trait PublisherEventTrait
{
    protected $message;

    /**
     * @var null
     */
    protected $routingKey;

    /**
     * @var int
     */
    protected $flags;

    /**
     * @var array
     */
    protected $attributes;

    /**
     * @var Exchange
     */
    protected $exchange;

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     */
    public function getRoutingKey()
    {
        return $this->routingKey;
    }

    /**
     * @return int
     */
    public function getFlags()
    {
        return $this->flags;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @return Exchange
     */
    public function getExchange()
    {
        return $this->exchange;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @param null $routingKey
     */
    public function setRoutingKey($routingKey)
    {
        $this->routingKey = $routingKey;
    }

    /**
     * @param int $flags
     */
    public function setFlags($flags)
    {
        $this->flags = $flags;
    }

    /**
     * @param array $attributes
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * @param Exchange $exchange
     */
    public function setExchange($exchange)
    {
        $this->exchange = $exchange;
    }
}
