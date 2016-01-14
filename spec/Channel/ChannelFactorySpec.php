<?php

namespace spec\Evaneos\Hector\Channel;

use Evaneos\Hector\Channel\Channel;
use Evaneos\Hector\Channel\ChannelFactory;
use Evaneos\Hector\Channel\ChannelRegistry;
use Evaneos\Hector\Connection\Connection;
use Evaneos\Hector\Connection\ConnectionRegistry;
use Evaneos\Hector\Identity\Identity;
use PhpSpec\ObjectBehavior;

class ChannelFactorySpec extends ObjectBehavior
{
    public function let(ConnectionRegistry $connectionRegistry, ChannelRegistry $channelRegistry)
    {
        $this->beConstructedWith($connectionRegistry, $channelRegistry);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(ChannelFactory::class);
    }

    public function it_should_create_channel_from_connection(
        ChannelRegistry $channelRegistry,
        Connection $connection,
        Identity $identity
    ) {
        $channel = $this->createFromConnection($connection, $identity);
        $channel->shouldBeAnInstanceOf(Channel::class);
        $channelRegistry->addChannel($channel)->shouldHaveBeenCalled();
    }

    public function it_should_create_channel_from_connection_name(
        ConnectionRegistry $connectionRegistry,
        ChannelRegistry $channelRegistry,
        Connection $connection,
        ChannelRegistry $channelRegistry,
        Identity $identity
    ) {
        $connectionRegistry->getConnection('default')->willReturn($connection);
        $channel = $this->createFromConnectionName('default', $identity);
        $channelRegistry->addChannel($channel)->shouldHaveBeenCalled();
        $channel->shouldBeAnInstanceOf(Channel::class);
    }
}
