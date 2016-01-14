<?php

namespace spec\Evaneos\Hector\Events;

use Evaneos\Hector\Events\PublisherEvent;
use Evaneos\Hector\Exchange\Exchange;
use PhpSpec\ObjectBehavior;

class PublisherEventSpec extends ObjectBehavior
{
    public function let(Exchange $exchange)
    {
        $this->beConstructedWith(
            'hello world',
            'routing_key',
            \AMQP_NOPARAM,
            ['foo' => 'bar'],
            $exchange
        );
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(PublisherEvent::class);
    }

    public function it_should_give_message()
    {
        $this->getMessage()->shouldReturn('hello world');
    }

    public function it_should_give_flags()
    {
        $this->getFlags()->shouldReturn(\AMQP_NOPARAM);
    }

    public function it_should_give_routing_key()
    {
        $this->getRoutingKey()->shouldReturn('routing_key');
    }

    public function it_should_give_attributes()
    {
        $this->getAttributes()->shouldReturn(['foo' => 'bar']);
    }

    public function it_should_give_exchange(Exchange $exchange)
    {
        $this->getExchange()->shouldReturn($exchange);
    }

    public function it_should_set_message()
    {
        $this->setMessage('foo');
        $this->getMessage()->shouldReturn('foo');
    }

    public function it_should_set_routing_key()
    {
        $this->setRoutingKey('bar');
        $this->getRoutingKey()->shouldReturn('bar');
    }

    public function it_should_set_flags()
    {
        $this->setFlags(500);
        $this->getFlags()->shouldReturn(500);
    }

    public function it_should_set_attributes()
    {
        $this->setAttributes([]);
        $this->getAttributes()->shouldReturn([]);
    }

    public function it_should_set_exchange(Exchange $exchange)
    {
        $this->setExchange($exchange);
        $this->getExchange()->shouldReturn($exchange);
    }
}
