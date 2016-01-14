<?php

namespace spec\Evaneos\Hector\Queue;

use Evaneos\Hector\Channel\Channel;
use Evaneos\Hector\Exception\NotFoundException;
use Evaneos\Hector\Exchange\Exchange;
use Evaneos\Hector\Queue\Context;
use Evaneos\Hector\Queue\Queue;
use Evaneos\Hector\Queue\QueueRegistry;
use PhpSpec\ObjectBehavior;

class QueueRegistrySpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(QueueRegistry::class);
    }

    public function it_should_add_queue(Queue $a, Queue $b)
    {
        $a->getFingerPrint()->shouldBeCalled()->willReturn('a');
        $b->getFingerPrint()->shouldBeCalled()->willReturn('b');

        $this->addQueue($a);
        $this->addQueue($b);
    }

    public function it_should_has_queue(Queue $a, Queue $b, Channel $channel, Exchange $exchange)
    {
        $a->getFingerPrint()->shouldBeCalled()->willReturn('a');
        $b->getFingerPrint()->shouldBeCalled()->willReturn('b');

        $this->addQueue($a);
        $this->addQueue($b);

        $a->isEqual('a', $channel, $exchange)->willReturn(true);
        $a->isEqual('b', $channel, $exchange)->willReturn(false);
        $b->isEqual('a', $channel, $exchange)->willReturn(false);
        $b->isEqual('b', $channel, $exchange)->willReturn(true);

        $this->hasQueue('a', $channel, $exchange)->shouldReturn(true);
        $this->hasQueue('b', $channel, $exchange)->shouldReturn(true);
    }

    public function it_should_give_queue(Queue $a, Queue $b, Channel $channel, Exchange $exchange)
    {
        $a->getFingerPrint()->shouldBeCalled()->willReturn('a');
        $b->getFingerPrint()->shouldBeCalled()->willReturn('b');

        $this->addQueue($a);
        $this->addQueue($b);

        $a->isEqual('a', $channel, $exchange)->willReturn(true);
        $a->isEqual('b', $channel, $exchange)->willReturn(false);
        $b->isEqual('a', $channel, $exchange)->willReturn(false);
        $b->isEqual('b', $channel, $exchange)->willReturn(true);

        $this->getQueue('a', $channel, $exchange)->shouldReturn($a);
        $this->getQueue('b', $channel, $exchange)->shouldReturn($b);
    }

    public function it_should_give_queue_context(Queue $a, Queue $b, Context $contextA, Context $contextB)
    {
        $a->getName()->willReturn('a');
        $b->getName()->willReturn('b');
        $a->getFingerPrint()->shouldBeCalled()->willReturn('a');
        $b->getFingerPrint()->shouldBeCalled()->willReturn('b');

        $a->getContext()->willReturn($contextA);
        $b->getContext()->willReturn($contextB);

        $this->addQueue($a);
        $this->addQueue($b);

        $this->getQueueContext('a')->shouldReturn($contextA);
    }

    public function it_should_throw_exception_if_no_queue(Channel $channel, Exchange $exchange)
    {
        $channel->getIdentity()->willReturn('bar');
        $exchange->getName()->willReturn('baz');

        $this->shouldThrow(
            new NotFoundException('Unable to find queue foo for channel bar and exchange baz')
        )->during('getQueue', ['foo', $channel, $exchange]);
    }

    public function it_should_throw_exception_if_no_context()
    {
        $this->shouldThrow(
            new NotFoundException('Unable to find queue foo')
        )->during('getQueueContext', ['foo']);
    }
}
