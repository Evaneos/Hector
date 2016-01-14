<?php

namespace spec\Evaneos\Hector\Consumer;

use Evaneos\Hector\Channel\Channel;
use Evaneos\Hector\Channel\ChannelFactory;
use Evaneos\Hector\Connection\Connection;
use Evaneos\Hector\Connection\ConnectionRegistry;
use Evaneos\Hector\Consumer\Consumer;
use Evaneos\Hector\Consumer\ConsumerFactory;
use Evaneos\Hector\Exchange\Exchange;
use Evaneos\Hector\Exchange\ExchangeFactory;
use Evaneos\Hector\Exchange\ExchangeRegistry;
use Evaneos\Hector\Identity\Identity;
use Evaneos\Hector\Queue\Queue;
use Evaneos\Hector\Queue\QueueFactory;
use Evaneos\Hector\Queue\QueueRegistry;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ConsumerFactorySpec extends ObjectBehavior
{
    public function let(
        ChannelFactory $channelFactory,
        QueueFactory $queueFactory,
        ExchangeFactory $exchangeFactory,
        ConnectionRegistry $connectionRegistry,
        ExchangeRegistry $exchangeRegistry,
        QueueRegistry $queueRegistry
    ) {
        $this->beConstructedWith(
            $channelFactory,
            $queueFactory,
            $exchangeFactory,
            $connectionRegistry,
            $exchangeRegistry,
            $queueRegistry
        );
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(ConsumerFactory::class);
    }

    public function it_should_create_from_connection(
        Connection $connection,
        ChannelFactory $channelFactory,
        Channel $channel,
        ExchangeFactory $exchangeFactory,
        Exchange $exchange,
        QueueFactory $queueFactory,
        Queue $queue,
        ExchangeRegistry $exchangeRegistry
    ) {
        $exchangeRegistry->hasExchange('exchange', $channel)->willReturn(false);
        $channelFactory->createFromConnection($connection, Argument::type(Identity::class))->willReturn($channel);
        $exchangeFactory->createNamed('exchange', $channel)->willReturn($exchange);
        $queueFactory->createNamed('queue', $channel, $exchange)->willReturn($queue);
        $consumer = $this->createFromConnection($connection, 'exchange', 'queue');
        $consumer->shouldBeAnInstanceOf(Consumer::class);
    }

    public function it_should_create_connection_name(
        ConnectionRegistry $connectionRegistry,
        Connection $connection,
        ChannelFactory $channelFactory,
        Channel $channel,
        ExchangeFactory $exchangeFactory,
        Exchange $exchange,
        QueueFactory $queueFactory,
        Queue $queue,
        ExchangeRegistry $exchangeRegistry
    ) {
        $exchangeRegistry->hasExchange('exchange', $channel)->willReturn(false);
        $connectionRegistry->getConnection('default')->willReturn($connection);
        $channelFactory->createFromConnection($connection, Argument::type(Identity::class))->willReturn($channel);
        $exchangeFactory->createNamed('exchange', $channel)->willReturn($exchange);
        $queueFactory->createNamed('queue', $channel, $exchange)->willReturn($queue);
        $consumer = $this->create('default', 'exchange', 'queue');
        $consumer->shouldBeAnInstanceOf(Consumer::class);
    }

    public function it_should_reuse_existing_exchange_when_its_same_channel(
        ConnectionRegistry $connectionRegistry,
        Connection $connection,
        ChannelFactory $channelFactory,
        Channel $channel,
        Exchange $exchange,
        QueueFactory $queueFactory,
        Queue $queue,
        ExchangeRegistry $exchangeRegistry,
        QueueRegistry $queueRegistry
    ) {
        $queueRegistry->hasQueue('queue', $channel, $exchange)->willReturn(false);
        $connectionRegistry->getConnection('default')->willReturn($connection);
        $channelFactory->createFromConnection($connection, Argument::type(Identity::class))->willReturn($channel);
        $exchangeRegistry->hasExchange('exchange', $channel)->willReturn(true);
        $exchangeRegistry->getExchange('exchange', $channel)->willReturn($exchange);
        $queueFactory->createNamed('queue', $channel, $exchange)->willReturn($queue);

        $consumer = $this->createFromConnection($connection, 'exchange', 'queue');

        $consumer->shouldBeAnInstanceOf(Consumer::class);
    }

    public function it_should_reuse_existing_exchange_when_its_same_channel_and_same_exchange(
        ConnectionRegistry $connectionRegistry,
        Connection $connection,
        ChannelFactory $channelFactory,
        Channel $channel,
        Exchange $exchange,
        Queue $queue,
        ExchangeRegistry $exchangeRegistry,
        QueueRegistry $queueRegistry
    ) {
        $connectionRegistry->getConnection('default')->willReturn($connection);
        $channelFactory->createFromConnection($connection, Argument::type(Identity::class))->willReturn($channel);
        $exchangeRegistry->hasExchange('exchange', $channel)->willReturn(true);
        $exchangeRegistry->getExchange('exchange', $channel)->willReturn($exchange);

        $queueRegistry->hasQueue('queue', $channel, $exchange)->willReturn(true);
        $queueRegistry->getQueue('queue', $channel, $exchange)->willReturn($queue);

        $consumer = $this->createFromConnection($connection, 'exchange', 'queue');

        $consumer->shouldBeAnInstanceOf(Consumer::class);
    }
}
