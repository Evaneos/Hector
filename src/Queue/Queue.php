<?php

namespace Evaneos\Hector\Queue;

use Evaneos\Hector\Channel\Channel;
use Evaneos\Hector\Exception\HectorException;
use Evaneos\Hector\Exchange\Exchange;

class Queue
{
    /** @var \AMQPQueue */
    private $queue;

    /** @var bool  */
    private $initialized;

    /** @var Context  */
    private $context;

    /** @var Channel  */
    private $channel;

    /** @var  Exchange */
    private $exchange;

    /** @var  string */
    private $name;

    /** @var  string */
    private $fingerPrint;

    /**
     * Queue constructor.
     *
     * @param string   $name
     * @param Channel  $channel
     * @param Exchange $exchange
     * @param Context  $context
     *
     * @throws HectorException
     */
    public function __construct($name = '', Channel $channel, Exchange $exchange, Context $context)
    {
        if (!is_string($name)) {
            throw new HectorException(sprintf(
                'Queue name should be a string, %s given',
                gettype($name)
            ));
        }

        $this->name        = $name;
        $this->channel     = $channel;
        $this->context     = $context;
        $this->exchange    = $exchange;
        $this->initialized = false;
        $this->fingerPrint = $this->computeFingerPrint($name, $channel, $exchange);
    }

    /**
     * @param string   $name
     * @param Channel  $channel
     * @param Exchange $exchange
     *
     * @return string
     */
    private function computeFingerPrint($name, Channel $channel, Exchange $exchange)
    {
        return sha1($channel->getIdentity() . $exchange->getName() . $name);
    }

    /**
     * @return Context
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @return string
     */
    public function getFingerPrint()
    {
        return $this->fingerPrint;
    }

    /**
     * @throws HectorException
     *
     * @return \AMQPQueue
     */
    public function getWrappedQueue()
    {
        if (false === $this->isInitialized()) {
            throw new HectorException('You must initialize exchange before access it');
        }

        return $this->queue;
    }

    /**
     * @param string   $name
     * @param Channel  $channel
     * @param Exchange $exchange
     *
     * @return string
     */
    public function isEqual($name, Channel $channel, Exchange $exchange)
    {
        return $this->computeFingerPrint($name, $channel, $exchange) === $this->fingerPrint;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isInitialized()
    {
        return $this->initialized;
    }

    /**
     * @param \AMQPQueue|null $queue
     *
     * @throws HectorException
     */
    public function initialize(\AMQPQueue $queue = null)
    {
        if (true === $this->isInitialized()) {
            throw new HectorException('Queue already initialized');
        }

        if (null === $queue) {
            $queue = new \AMQPQueue($this->channel->getWrappedChannel());
        }

        $this->queue = $queue;
        $this->queue->setName($this->getName());
        $this->queue->bind($this->exchange->getName());
        $this->queue->setFlags($this->context->getFlags());
        $this->queue->setArguments($this->context->getArguments());
        $this->queue->declareQueue();
        $this->initialized = true;
    }
}
