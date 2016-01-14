<?php

namespace spec\Evaneos\Hector\Exchange;

use Evaneos\Hector\Channel\Channel;
use Evaneos\Hector\Exception\HectorException;
use Evaneos\Hector\Exchange\Context;
use Evaneos\Hector\Exchange\Exchange;
use PhpSpec\ObjectBehavior;
use Ramsey\Uuid\Uuid;

class ExchangeSpec extends ObjectBehavior
{
    public function let(Channel $channel, Context $context)
    {
        $this->beConstructedWith('exchange', $channel, $context);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(Exchange::class);
    }

    public function it_should_have_a_fingerprint(Channel $channel, Context $context)
    {
        $channelId = Uuid::uuid4();
        $channel->getIdentity()->willReturn($channelId);
        $this->beConstructedWith('exchange', $channel, $context);
        $this->getFingerPrint()->shouldNotBeLike($channelId);
        $this->getFingerPrint()->shouldBeString();
    }

    public function it_should_not_be_initialized()
    {
        $this->isInitialized()->shouldReturn(false);
    }

    public function it_should_give_channel(Channel $channel)
    {
        $this->getChannel()->shouldReturn($channel);
    }

    public function it_should_give_context(Context $context)
    {
        $this->getContext()->shouldReturn($context);
    }

    public function it_should_give_name()
    {
        $this->getName()->shouldReturn('exchange');
    }

    public function it_should_throw_exception_when_access_to_exchange_and_not_initialized()
    {
        $this->shouldThrow(
            new HectorException('You must initialize exchange before access it')
        )->during('getWrappedExchange');
    }

    public function it_should_compare_fingerprint(Channel $a, Channel $b, Context $context)
    {
        $a->getIdentity()->willReturn('a');
        $b->getIdentity()->willReturn('b');

        $this->beConstructedWith('exchange', $a, $context);

        $this->isEqual('exchange', $b)->shouldReturn(false);
        $this->isEqual('exchange', $a)->shouldReturn(true);
        $this->isEqual('exchange2', $a)->shouldReturn(false);
    }

    public function it_should_initialize_exchange(
        Channel $channel,
        Context $context,
        \AMQPChannel $AMQPchannel,
        \AMQPExchange $AMQPExchange
    ) {
        $channel->getWrappedChannel()->willReturn($AMQPchannel);
        $channel->getIdentity()->willReturn('channelID');
        $context->getType()->willReturn('direct');
        $context->getArguments()->willReturn(['foo' => 'bar']);
        $context->getFlags()->willReturn(1);

        $context->getType()->shouldBeCalled();
        $context->getArguments()->shouldBeCalled();
        $context->getFlags()->shouldBeCalled();

        $this->beConstructedWith('exchange', $channel, $context);

        $this->initialize($AMQPExchange);
        $this->isInitialized()->shouldReturn(true);
        $this->getWrappedExchange()->shouldReturn($AMQPExchange);
    }
}
