<?php

namespace Evaneos\Hector\Connection;

class Connection
{
    /** @var \AMQPConnection */
    private $connection;

    /** @var  string */
    private $name;

    /**
     * Connection constructor.
     *
     * @param \AMQPConnection $connection
     * @param string          $name
     */
    public function __construct(\AMQPConnection $connection, $name)
    {
        $this->connection = $connection;
        $this->name       = $name;
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
    public function connect()
    {
        if (false === $this->connection->isConnected()) {
            return $this->connection->connect();
        }
    }

    /**
     * @return bool
     */
    public function disconnect()
    {
        if (true === $this->connection->isConnected()) {
            return $this->connection->disconnect();
        }
    }

    /**
     * @return bool
     */
    public function isConnected()
    {
        return $this->connection->isConnected();
    }

    /**
     * @return \AMQPConnection
     */
    public function getWrappedConnection()
    {
        return $this->connection;
    }
}
