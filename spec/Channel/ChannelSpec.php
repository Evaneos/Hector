<?php

namespace spec\Evaneos\Hector\Channel;

use Evaneos\Hector\Channel\Channel;
use Evaneos\Hector\Connection\Connection;
use Evaneos\Hector\Exception\HectorException;
use Evaneos\Hector\Identity\Identity;
use PhpSpec\ObjectBehavior;

class ChannelSpec extends ObjectBehavior
{
    public function let(Connection $connection, Identity $identity, \AMQPChannel $channel)
    {
        $identity->getIdentifier()->willReturn('foo');

        $this->beConstructedWith($connection, $identity, $channel);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(Channel::class);
    }

    public function it_should_not_be_initialized_on_construct_if_no_channel(Connection $connection, Identity $identity)
    {
        $this->beConstructedWith($connection, $identity);
        $this->isInitialized()->shouldReturn(false);
    }

    public function it_should_throw_exception_if_not_initialized(Connection $connection, Identity $identity)
    {
        $this->beConstructedWith($connection, $identity);
        $this->shouldThrow(HectorException::class)->during('getWrappedChannel');
    }

    public function it_should_initialized_channel(Connection $connection, Identity $identity, \AMQPConnection $AMQPConnection)
    {
        $this->beConstructedWith($connection, $identity);
        $connection->getWrappedConnection()->willReturn($AMQPConnection);
        $connection->connect()->shouldBeCalled();

        $this->shouldThrow(
            new \AMQPConnectionException('Could not create channel. No connection available.')
        )->during('initialize');
    }

    public function it_should_be_initialized_if_channel_is_passed()
    {
        $this->isInitialized()->shouldReturn(true);
    }

    public function it_should_start_transaction(\AMQPChannel $channel)
    {
        $channel->startTransaction()->shouldBeCalled();
        $this->startTransaction();
    }

    public function it_should_commit_transaction(\AMQPChannel $channel)
    {
        $channel->commitTransaction()->shouldBeCalled();
        $this->commitTransaction();
    }

    public function it_should_rollback_transaction(\AMQPChannel $channel)
    {
        $channel->rollbackTransaction()->shouldBeCalled();
        $this->rollbackTransaction();
    }

    public function it_should_make_transaction_and_rollback(\AMQPChannel $channel)
    {
        $channel->startTransaction()->shouldBeCalled();
        $channel->rollbackTransaction()->shouldBeCalled();

        $this->transaction(function (Channel $channel) {
            return false;
        });
    }

    public function it_should_make_transaction_and_commit(\AMQPChannel $channel)
    {
        $channel->startTransaction()->shouldBeCalled();
        $channel->commitTransaction()->shouldBeCalled();

        $this->transaction(function (Channel $channel) {
            return true;
        });
    }

    public function it_should_rollback_if_transaction_failed(\AMQPChannel $channel)
    {
        $channel->startTransaction()->shouldBeCalled();
        $channel->commitTransaction()->shouldNotBeCalled();
        $channel->rollbackTransaction()->shouldBeCalled();

        $exception = new \Exception('Whhooops');

        $process = function (Channel $channel) use ($exception) {
            throw $exception;
        };

        $this->shouldThrow(
            new HectorException('Transaction failed', 255, $exception)
        )->during('transaction', [$process]);
    }

    public function it_should_give_wrapped_channel(\AMQPChannel $channel)
    {
        $this->getWrappedChannel()->shouldReturn($channel);
    }

    public function it_should_give_identity()
    {
        $this->getIdentity()->shouldReturn('foo');
    }
}
