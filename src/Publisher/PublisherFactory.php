<?php

namespace Evaneos\Hector\Publisher;

use Evaneos\Hector\Channel\Channel;
use Evaneos\Hector\Channel\ChannelFactory;
use Evaneos\Hector\Connection\Connection;
use Evaneos\Hector\Connection\ConnectionRegistry;
use Evaneos\Hector\Exchange\ExchangeFactory;
use Evaneos\Hector\Exchange\ExchangeRegistry;
use Evaneos\Hector\Identity\Identity;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PublisherFactory
{
    /** @var ConnectionRegistry  */
    private $connectionRegistry;

    /** @var ExchangeFactory  */
    private $exchangeFactory;

    /** @var ChannelFactory  */
    private $channelFactory;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var  ExchangeRegistry */
    private $exchangeRegistry;

    /**
     * PublisherFactory constructor.
     *
     * @param ConnectionRegistry       $connectionRegistry
     * @param ExchangeFactory          $exchangeFactory
     * @param ChannelFactory           $channelFactory
     * @param EventDispatcherInterface $eventDispatcher
     * @param ExchangeRegistry         $exchangeRegistry
     */
    public function __construct(
        ConnectionRegistry $connectionRegistry,
        ExchangeFactory $exchangeFactory,
        ChannelFactory $channelFactory,
        EventDispatcherInterface $eventDispatcher = null,
        ExchangeRegistry $exchangeRegistry
    ) {
        $this->connectionRegistry = $connectionRegistry;
        $this->exchangeFactory    = $exchangeFactory;
        $this->channelFactory     = $channelFactory;
        $this->exchangeRegistry   = $exchangeRegistry;
    }

    /**
     * @param Connection $connection
     * @param string     $exchangeName
     * @param array      $options
     *
     * @return Publisher
     */
    public function createFromConnection(Connection $connection, $exchangeName, Channel $channel = null, array $options = [])
    {
        $identity = new Identity();

        if (null === $channel) {
            $channel = $this->channelFactory->createFromConnection($connection, $identity);
        }

        if (!$this->exchangeRegistry->hasExchange($exchangeName, $channel)) {
            $exchange = $this->exchangeFactory->createNamed($exchangeName, $channel);
        } else {
            $exchange = $this->exchangeRegistry->getExchange($exchangeName, $channel);
        }

        $publisher = new Publisher(
            $identity,
            $this->eventDispatcher,
            $connection,
            $channel,
            $exchange,
            $options
        );

        return $publisher;
    }

    /**
     * @param string $connectionName
     * @param string $exchangeName
     * @param array  $options
     *
     * @return Publisher
     */
    public function create($connectionName, $exchangeName, Channel $channel = null, array $options = [])
    {
        $connection = $this->connectionRegistry->getConnection($connectionName);

        return $this->createFromConnection($connection, $exchangeName, $channel, $options);
    }
}
