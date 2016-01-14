<?php

namespace Evaneos\Hector\Channel;

use Evaneos\Hector\Connection\Connection;
use Evaneos\Hector\Connection\ConnectionRegistry;
use Evaneos\Hector\Identity\Identity;

class ChannelFactory
{
    /**
     * @var ChannelRegistry
     */
    private $channelRegistry;

    /**
     * @var ConnectionRegistry
     */
    private $connectionRegistry;

    /**
     * ChannelFactory constructor.
     *
     * @param ConnectionRegistry $connectionRegistry
     * @param ChannelRegistry    $channelRegistry
     */
    public function __construct(ConnectionRegistry $connectionRegistry, ChannelRegistry $channelRegistry)
    {
        $this->channelRegistry    = $channelRegistry;
        $this->connectionRegistry = $connectionRegistry;
    }

    /**
     * @param Connection $connection
     * @param Identity   $identity
     *
     * @return Channel
     */
    public function createFromConnection(Connection $connection, Identity $identity)
    {
        $channel = new Channel($connection, $identity);

        $this->channelRegistry->addChannel($channel);

        return $channel;
    }

    /**
     * @param string   $connectionName
     * @param Identity $identity
     *
     * @return Channel
     */
    public function createFromConnectionName($connectionName, Identity $identity)
    {
        $connection = $this->connectionRegistry->getConnection($connectionName);

        return $this->createFromConnection($connection, $identity);
    }
}
