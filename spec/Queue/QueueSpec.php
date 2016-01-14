<?php

namespace spec\Evaneos\Hector\Queue;

use Evaneos\Hector\Channel\Channel;
use Evaneos\Hector\Exception\HectorException;
use Evaneos\Hector\Exchange\Exchange;
use Evaneos\Hector\Queue\Context;
use Evaneos\Hector\Queue\Queue;
use PhpSpec\ObjectBehavior;

class QueueSpec extends ObjectBehavior
{
    public function let(
        Channel $channel,
        Exchange $exchange,
        Context $context
    ) {
        $this->beConstructedWith('queue', $channel, $exchange, $context);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(Queue::class);
    }

    public function it_should_throw_exception_if_name_is_not_string(
        Channel $channel,
        Exchange $exchange,
        Context $context
    ) {
        $this->beConstructedWith(true, $channel, $exchange, $context);
        $this->shouldThrow(
            new HectorException('Queue name should be a string, boolean given')
        )->duringInstantiation(new \StdClass(), $channel, $exchange, $context);
    }

    public function it_should_give_context(Context $context)
    {
        $this->getContext()->shouldReturn($context);
    }

    public function it_should_give_fingerprint(
        Channel $channel,
        Exchange $exchange
    ) {
        $channel->getIdentity()->willReturn('channel');
        $exchange->getName()->willReturn('exchange');

        $this->getFingerPrint()->shouldReturn(
            sha1('channel' . 'exchange' . 'queue')
        );
    }

    public function it_should_throw_exception_if_give_wrapped_and_not_initialized(

    ) {
        $this->shouldThrow(
            new HectorException('You must initialize exchange before access it')
        )->during('getWrappedQueue');
    }

    public function it_should_give_wrapped_queue(\AMQPQueue $queue, Context $context)
    {
        $context->getArguments()->willReturn([])->shouldBeCalled();
        $context->getFlags()->shouldBeCalled();

        $this->initialize($queue);

        $this->getWrappedQueue()->shouldReturn($queue);
    }

    public function it_initialized_once(\AMQPQueue $queue, Context $context)
    {
        $context->getArguments()->willReturn([]);
        $context->getFlags()->willReturn(1);

        $this->initialize($queue);

        $this->shouldThrow(
            new HectorException('Queue already initialized')
        )->during('initialize');
    }

    public function it_should_initialize(
        \AMQPQueue $queue,
        Exchange $exchange,
        Context $context
    ) {
        $exchange->getName()->willReturn('exchange');
        $context->getFlags()->willReturn(1234);
        $context->getArguments()->willReturn([
            'foo' => 'bar',
        ]);

        $queue->setName('queue')->shouldBeCalled();
        $queue->bind('exchange')->shouldBeCalled();
        $queue->setFlags(1234)->shouldBeCalled();
        $queue->setArguments(['foo' => 'bar'])->shouldBeCalled();
        $queue->declareQueue()->shouldBeCalled();

        $this->initialize($queue);

        $this->isInitialized()->shouldReturn(true);
    }
}
