<?php

namespace spec\Evaneos\Hector\Consumer;

use Evaneos\Hector\Channel\Channel;
use Evaneos\Hector\Connection\Connection;
use Evaneos\Hector\Consumer\Consumer;
use Evaneos\Hector\Exchange\Exchange;
use Evaneos\Hector\Identity\Identity;
use Evaneos\Hector\Queue\Queue;
use PhpSpec\ObjectBehavior;

class ConsumerSpec extends ObjectBehavior
{
    public function let(Identity $identity, Connection $connection, Channel $channel, Exchange $exchange, Queue $queue)
    {
        $this->beConstructedWith($identity, $connection, $channel, $exchange, $queue);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(Consumer::class);
    }

    public function it_should_configure_connection(Connection $connection, Channel $channel, Exchange $exchange, Queue $queue)
    {
        $connection->connect()->shouldBeCalled();

        $channel->isInitialized()->willReturn(false);
        $channel->initialize()->shouldBeCalled();

        $exchange->isInitialized()->willReturn(false);
        $exchange->initialize()->shouldBeCalled();

        $queue->isInitialized()->willReturn(false);
        $queue->initialize()->shouldBeCalled();

        $this->initialize();
    }

    public function it_should_give_exchange(Exchange $exchange)
    {
        $this->getExchange()->shouldBeLike($exchange);
    }

    public function it_should_give_queue(Queue $queue)
    {
        $this->getQueue()->shouldBeLike($queue);
    }

    public function it_should_give_connection(Connection $connection)
    {
        $this->getConnection()->shouldBeLike($connection);
    }

    public function it_should_give_identity(Identity $identity)
    {
        $identifier = 'foo.bar';
        $identity->getIdentifier()->willReturn($identifier);
        $this->getIdentity()->shouldBe($identifier);
    }
}
