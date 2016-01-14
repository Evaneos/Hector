<?php

namespace Evaneos\Hector\Exchange;

use Evaneos\Hector\Channel\Channel;
use Evaneos\Hector\Exception\HectorException;

class Exchange
{
    /** @var Context */
    private $context;

    /** @var  \AMQPExchange */
    private $exchange;

    /** @var  bool */
    private $initialized;

    /** @var  Channel */
    private $channel;

    /** @var  string */
    private $name;

    /** @var string */
    private $fingerPrint;

    /**
     * Exchange constructor.
     *
     * @param string  $name
     * @param Channel $channel
     * @param Context $context
     */
    public function __construct($name, Channel $channel, Context $context)
    {
        $this->channel     = $channel;
        $this->name        = $name;
        $this->context     = $context;
        $this->initialized = false;
        $this->fingerPrint = sha1($this->channel->getIdentity() . $this->name);
    }

    /**
     * @return bool
     */
    public function isInitialized()
    {
        return $this->initialized;
    }

    /**
     * @return Channel
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @return Context
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param \AMQPExchange|null $exchange
     *
     * @throws HectorException
     */
    public function initialize(\AMQPExchange $exchange = null)
    {
        if (null == $exchange) {
            $exchange = new \AMQPExchange($this->channel->getWrappedChannel());
        }

        $this->exchange = $exchange;
        $this->exchange->setName($this->getName());
        $this->exchange->setType($this->context->getType());
        $this->exchange->setArguments($this->context->getArguments());
        $this->exchange->setFlags((int) $this->context->getFlags());
        $this->exchange->declareExchange();
        $this->initialized = true;
    }

    /**
     * @return string
     */
    public function getFingerPrint()
    {
        return $this->fingerPrint;
    }

    /**
     * @param string  $name
     * @param Channel $channel
     *
     * @return bool
     */
    public function isEqual($name, Channel $channel)
    {
        return sha1($channel->getIdentity() . $name) === $this->fingerPrint;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @throws HectorException
     *
     * @return \AMQPExchange
     */
    public function getWrappedExchange()
    {
        if (false === $this->isInitialized()) {
            throw new HectorException('You must initialize exchange before access it');
        }

        return $this->exchange;
    }
}
