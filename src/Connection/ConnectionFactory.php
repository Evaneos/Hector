<?php

namespace Evaneos\Hector\Connection;

use Evaneos\Hector\Exception\HectorException;

class ConnectionFactory
{
    /**
     * @var ConnectionRegistry
     */
    private $registry;

    /** @var array */
    private $configs;

    /**
     * ConnectionFactory constructor.
     *
     * @param ConnectionRegistry $registry
     * @param array              $configs
     */
    public function __construct(ConnectionRegistry $registry, array $configs)
    {
        $this->registry = $registry;
        $this->configs  = $configs;
    }

    /**
     * @param $name
     *
     * @throws HectorException
     *
     * @return Connection
     */
    public function createNamed($name)
    {
        if (!isset($this->configs[$name])) {
            throw new HectorException(sprintf('Unable to load config for connection %s', $name));
        }

        $connection = new Connection(new \AMQPConnection($this->configs[$name]), $name);
        $this->registry->addConnection($connection);

        return $connection;
    }
}
