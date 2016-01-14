<?php

namespace spec\Evaneos\Hector\Publisher;

use Evaneos\Hector\Channel\Channel;
use Evaneos\Hector\Channel\ChannelFactory;
use Evaneos\Hector\Connection\Connection;
use Evaneos\Hector\Connection\ConnectionRegistry;
use Evaneos\Hector\Exchange\Exchange;
use Evaneos\Hector\Exchange\ExchangeFactory;
use Evaneos\Hector\Exchange\ExchangeRegistry;
use Evaneos\Hector\Identity\Identity;
use Evaneos\Hector\Publisher\Publisher;
use Evaneos\Hector\Publisher\PublisherFactory;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PublisherFactorySpec extends ObjectBehavior
{
    public function let(
        ConnectionRegistry $connectionRegistry,
        ExchangeFactory $exchangeFactory,
        ChannelFactory $channelFactory,
        EventDispatcherInterface $eventDispatcher,
        ExchangeRegistry $exchangeRegistry
    ) {
        $this->beConstructedWith(
            $connectionRegistry,
            $exchangeFactory,
            $channelFactory,
            $eventDispatcher,
            $exchangeRegistry
        );
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(PublisherFactory::class);
    }

    public function it_should_create_publisher_from_connection(
        Connection $connection,
        ChannelFactory $channelFactory,
        ExchangeRegistry $exchangeRegistry,
        ExchangeFactory $exchangeFactory,
        Channel $channel,
        Exchange $exchange
    ) {
        $channelFactory->createFromConnection($connection, Argument::type(Identity::class))->willReturn($channel);
        $exchangeRegistry->hasExchange('exchange', $channel)->willReturn(false);
        $exchangeFactory->createNamed('exchange', $channel)->willReturn($exchange);
        $this->createFromConnection($connection, 'exchange')->shouldReturnAnInstanceOf(Publisher::class);
    }

    public function it_should_create_exchange_from_connection_name(
        ConnectionRegistry $connectionRegistry,
        Connection $connection,
        ChannelFactory $channelFactory,
        ExchangeRegistry $exchangeRegistry,
        ExchangeFactory $exchangeFactory,
        Channel $channel,
        Exchange $exchange
    ) {
        $connectionRegistry->getConnection('default')->willReturn($connection);
        $channelFactory->createFromConnection($connection, Argument::type(Identity::class))->willReturn($channel);
        $exchangeRegistry->hasExchange('exchange', $channel)->willReturn(false);
        $exchangeFactory->createNamed('exchange', $channel)->willReturn($exchange);

        $this->create('default', 'exchange')->shouldReturnAnInstanceOf(Publisher::class);
    }
}
