<?php

namespace Evaneos\Hector\Consumer;

use Evaneos\Hector\Channel\Channel;
use Evaneos\Hector\Connection\Connection;
use Evaneos\Hector\Exchange\Exchange;
use Evaneos\Hector\Identity\Identity;
use Evaneos\Hector\Queue\Queue;

class Consumer
{
    /** @var  string */
    private $identity;

    /** @var  Channel */
    private $channel;

    /** @var  Exchange */
    private $exchange;

    /** @var  Queue */
    private $queue;

    /** @var  Connection */
    private $connection;

    /** @var  bool */
    private $initialized;

    /**
     * Consumer constructor.
     *
     * @param Identity   $identity
     * @param Connection $connection
     * @param Channel    $channel
     * @param Exchange   $exchange
     * @param Queue      $queue
     */
    public function __construct(Identity $identity, Connection $connection, Channel $channel, Exchange $exchange, Queue $queue)
    {
        $this->channel     = $channel;
        $this->exchange    = $exchange;
        $this->queue       = $queue;
        $this->connection  = $connection;
        $this->identity    = $identity;
        $this->initialized = false;
    }

    public function initialize()
    {
        $this->connection->connect();

        if (false === $this->channel->isInitialized()) {
            $this->channel->initialize();
        }

        if (false === $this->exchange->isInitialized()) {
            $this->exchange->initialize();
        }

        if (false === $this->queue->isInitialized()) {
            $this->queue->initialize();
        }

        $this->initialized = true;
    }

    /**
     * @return Exchange
     */
    public function getExchange()
    {
        return $this->exchange;
    }

    /**
     * @return Queue
     */
    public function getQueue()
    {
        return $this->queue;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @return string
     */
    public function getIdentity()
    {
        return $this->identity->getIdentifier();
    }
}
