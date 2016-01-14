<?php

namespace Evaneos\Hector\Consumer;

use Evaneos\Hector\Channel\ChannelFactory;
use Evaneos\Hector\Connection\Connection;
use Evaneos\Hector\Connection\ConnectionRegistry;
use Evaneos\Hector\Exchange\ExchangeFactory;
use Evaneos\Hector\Exchange\ExchangeRegistry;
use Evaneos\Hector\Identity\Identity;
use Evaneos\Hector\Queue\QueueFactory;
use Evaneos\Hector\Queue\QueueRegistry;

class ConsumerFactory
{
    /** @var ConnectionRegistry  */
    private $connectionRegistry;

    /** @var  QueueFactory */
    private $queueFactory;

    /** @var  ExchangeFactory */
    private $exchangeFactory;

    /** @var ChannelFactory */
    private $channelFactory;

    /** @var  ExchangeRegistry */
    private $exchangeRegistry;

    /** @var  QueueRegistry */
    private $queueRegistry;

    /**
     * ConsumerFactory constructor.
     *
     * @param ChannelFactory     $channelFactory
     * @param QueueFactory       $queueFactory
     * @param ExchangeFactory    $exchangeFactory
     * @param ConnectionRegistry $connectionRegistry
     * @param ExchangeRegistry   $exchangeRegistry
     * @param QueueRegistry      $queueRegistry
     */
    public function __construct(
        ChannelFactory $channelFactory,
        QueueFactory $queueFactory,
        ExchangeFactory $exchangeFactory,
        ConnectionRegistry $connectionRegistry,
        ExchangeRegistry $exchangeRegistry,
        QueueRegistry $queueRegistry
    ) {
        $this->channelFactory     = $channelFactory;
        $this->exchangeFactory    = $exchangeFactory;
        $this->queueFactory       = $queueFactory;
        $this->connectionRegistry = $connectionRegistry;
        $this->exchangeRegistry   = $exchangeRegistry;
        $this->queueRegistry      = $queueRegistry;
    }

    /**
     * @param Connection $connection
     * @param string     $exchangeName
     * @param string     $queueName
     *
     * @return Consumer
     */
    public function createFromConnection(Connection $connection, $exchangeName, $queueName)
    {
        $identity = new Identity();
        $channel  = $this->channelFactory->createFromConnection($connection, $identity);

        if (!$this->exchangeRegistry->hasExchange($exchangeName, $channel)) {
            $exchange = $this->exchangeFactory->createNamed($exchangeName, $channel);
        } else {
            $exchange = $this->exchangeRegistry->getExchange($exchangeName, $channel);
        }

        if (!$this->queueRegistry->hasQueue($queueName, $channel, $exchange)) {
            $queue = $this->queueFactory->createNamed($queueName, $channel, $exchange);
        } else {
            $queue = $this->queueRegistry->getQueue($queueName, $channel, $exchange);
        }

        $consumer = new Consumer($identity, $connection, $channel, $exchange, $queue);

        return $consumer;
    }

    /**
     * @param string $connectionName
     * @param string $exchangeName
     * @param string $queueName
     *
     * @return Consumer
     */
    public function create($connectionName, $exchangeName, $queueName)
    {
        $connection = $this->connectionRegistry->getConnection($connectionName);

        return $this->createFromConnection($connection, $exchangeName, $queueName);
    }
}
