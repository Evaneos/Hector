<?php

namespace spec\Evaneos\Hector\Exchange;

use Evaneos\Hector\Exchange\Context;
use PhpSpec\ObjectBehavior;

class ContextSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith('direct', 123, ['foo' => 'bar']);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(Context::class);
    }

    public function it_should_create_context_from_config()
    {
        $configs = [
            'type'      => 'foo',
            'flags'     => 255,
            'arguments' => [
                'bar' => 'baz',
                'foo' => 'bar',
            ],
        ];

        $context = $this->createFromConfig($configs);

        $context->shouldBeLike(new Context(
            $configs['type'],
            $configs['flags'],
            $configs['arguments']
        ));
    }

    public function it_should_give_type()
    {
        $this->getType()->shouldReturn('direct');
    }

    public function it_should_give_flags()
    {
        $this->getFlags()->shouldReturn(123);
    }

    public function it_should_give_arguments()
    {
        $this->getArguments()->shouldReturn(['foo' => 'bar']);
    }
}
