<?php

namespace spec\Evaneos\Hector\Queue;

use Evaneos\Hector\Channel\Channel;
use Evaneos\Hector\Context\ContextRegistry;
use Evaneos\Hector\Exchange\Exchange;
use Evaneos\Hector\Queue\Context;
use Evaneos\Hector\Queue\Queue;
use Evaneos\Hector\Queue\QueueFactory;
use Evaneos\Hector\Queue\QueueRegistry;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class QueueFactorySpec extends ObjectBehavior
{
    public function let(ContextRegistry $contextRegistry, QueueRegistry $queueRegistry)
    {
        $this->beConstructedWith($contextRegistry, $queueRegistry);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(QueueFactory::class);
    }

    public function it_should_create_queue_from_name(
        Channel $channel,
        Exchange $exchange,
        ContextRegistry $contextRegistry,
        QueueRegistry $queueRegistry,
        Context $context
    ) {
        $contextRegistry->getQueueContext('queue')->willReturn($context);
        $queueRegistry->addQueue(Argument::type(Queue::class))->shouldBeCalled();

        $queue = $this->createNamed('queue', $channel, $exchange);

        $queueRegistry->addQueue($queue)->shouldHaveBeenCalled();
    }
}
