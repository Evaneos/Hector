<?php

namespace spec\Evaneos\Hector\Connection;

use Evaneos\Hector\Connection\Connection;
use PhpSpec\ObjectBehavior;

class ConnectionSpec extends ObjectBehavior
{
    public function let(\AmqpConnection $connection)
    {
        $this->beConstructedWith($connection, 'default');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(Connection::class);
    }

    public function it_should_have_name()
    {
        $this->getName()->shouldReturn('default');
    }

    public function it_should_connect(\AMQPConnection $connection)
    {
        $connection->connect()->willReturn(false);
        $connection->isConnected()->shouldBeCalled();

        $this->connect();
    }

    public function it_should_prevent_multiple_connection_on_same_endpoint(\AMQPConnection $connection)
    {
        $connection->isConnected()->willReturn(true);
        $connection->connect()->shouldNotBeCalled();

        $this->connect();
    }

    public function it_should_disconnect(\AMQPConnection $connection)
    {
        $connection->isConnected()->willReturn(true);
        $connection->disconnect()->shouldBeCalled();

        $this->disconnect();
    }

    public function it_should_ensure_disconnection_on_same_endpoint(\AMQPConnection $connection)
    {
        $connection->isConnected()->willReturn(false);
        $connection->disconnect()->shouldNotBeCalled();

        $this->disconnect();
    }

    public function it_should_give_connection_status(\AMQPConnection $connection)
    {
        $connection->isConnected()->willReturn(true);
        $this->isConnected()->shouldReturn(true);

        $connection->isConnected()->willReturn(false);
        $this->isConnected()->shouldReturn(false);
    }

    public function it_should_give_wrapped_amqp_connection(\AMQPConnection $connection)
    {
        $this->getWrappedConnection()->shouldReturn($connection);
    }
}
