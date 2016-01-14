<?php

namespace Evaneos\Hector\Channel;

use Evaneos\Hector\Connection\Connection;
use Evaneos\Hector\Exception\HectorException;
use Evaneos\Hector\Identity\Identity;

class Channel
{
    /** @var Connection  */
    private $connection;

    /** @var Identity  */
    private $identity;

    /** @var \AMQPChannel  */
    private $channel;

    /** @var  bool */
    private $initialized;

    /**
     * Channel constructor.
     *
     * @param Connection    $connection
     * @param Identity|null $identity
     */
    public function __construct(Connection $connection, Identity $identity, \AMQPChannel $channel = null)
    {
        $this->identity   = $identity;
        $this->connection = $connection;

        if (null === $channel) {
            $this->initialized = false;
        } else {
            $this->connection->connect();
            $this->channel     = $channel;
            $this->initialized = true;
        }
    }

    public function initialize()
    {
        $this->connection->connect();
        $this->channel     = new \AMQPChannel($this->connection->getWrappedConnection());
        $this->initialized = true;
    }

    /**
     * @return bool
     */
    public function isInitialized()
    {
        return $this->initialized;
    }

    /**
     * @return bool
     */
    public function startTransaction()
    {
        if(!$this->isInitialized()){
            $this->initialize();
        }

        return $this->channel->startTransaction();
    }

    /**
     * @return bool
     */
    public function commitTransaction()
    {
        if(!$this->isInitialized()){
            $this->initialize();
        }

        return $this->channel->commitTransaction();
    }

    /**
     * @return bool
     */
    public function rollbackTransaction()
    {
        if(!$this->isInitialized()){
            $this->initialize();
        }

        return $this->channel->rollbackTransaction();
    }

    /**
     * @param \Closure $closure
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function transaction(\Closure $closure)
    {
        if(!$this->isInitialized()){
            $this->initialize();
        }

        try {
            $this->startTransaction();

            $result = $closure($this);

            if (!$result) {
                return $this->rollbackTransaction();
            }

            return $this->commitTransaction();
        } catch (\Exception $e) {
            $this->rollbackTransaction();
            throw new HectorException('Transaction failed', 255, $e);
        }
    }

    /**
     * @throws HectorException
     *
     * @return \AMQPChannel
     */
    public function getWrappedChannel()
    {
        if (false === $this->isInitialized()) {
            throw new HectorException('You must initialize exchange before access it');
        }

        return $this->channel;
    }

    /**
     * @return string
     */
    public function getIdentity()
    {
        return $this->identity->getIdentifier();
    }
}
