<?php

namespace spec\Evaneos\Hector\Queue;

use Evaneos\Hector\Queue\Context;
use PhpSpec\ObjectBehavior;

class ContextSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith('routingKey', 1, ['foo' => 'bar']);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(Context::class);
    }

    public function it_should_give_routing_key()
    {
        $this->getRoutingKey()->shouldReturn('routingKey');
    }

    public function it_should_give_flags()
    {
        $this->getFlags()->shouldReturn(1);
    }

    public function it_should_give_arguments()
    {
        $this->getArguments()->shouldReturn([
            'foo' => 'bar',
        ]);
    }

    public function it_should_create_from_config()
    {
        $context = $this->createFromConfig([
            'routing_key' => 'routingKey',
            'flags'       => 34,
            'arguments'   => ['bar' => 'baz'],
        ]);

        $context->shouldBeLike(new Context('routingKey', 34, ['bar' => 'baz']));
    }
}
