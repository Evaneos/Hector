<?php

namespace Evaneos\Hector\Connection;

use Evaneos\Hector\Exception\NotFoundException;

class ConnectionRegistry
{
    /** @var array  */
    private $connections;

    public function __construct()
    {
        $this->connections = [];
    }

    /**
     * @param Connection $connection
     */
    public function addConnection(Connection $connection)
    {
        $this->connections[$connection->getName()] = $connection;
    }

    /**
     * @param $name
     *
     * @throws NotFoundException
     *
     * @return mixed
     */
    public function getConnection($name)
    {
        if (!isset($this->connections[$name])) {
            throw new NotFoundException(sprintf(
                'Connection %s not registered, available are [ %s ]'),
                $name,
                implode(', ', array_keys($this->connections))
            );
        }

        return $this->connections[$name];
    }
}
