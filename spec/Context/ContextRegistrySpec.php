<?php

namespace spec\Evaneos\Hector\Context;

use Evaneos\Hector\Context\ContextRegistry;
use Evaneos\Hector\Exchange\Context as ExchangeContext;
use Evaneos\Hector\Queue\Context as QueueContext;
use PhpSpec\ObjectBehavior;

class ContextRegistrySpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith();
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(ContextRegistry::class);
    }

    public function it_should_add_queue_context(
        QueueContext $a,
        QueueContext $b
    ) {
        $this->addQueueContext('a', $a);
        $this->addQueueContext('b', $b);

        $this->getQueueContext('a')->shouldReturn($a);
        $this->getQueueContext('b')->shouldReturn($b);
    }

    public function it_should_add_exchange_context(
        ExchangeContext $a,
        ExchangeContext $b
    ) {
        $this->addExchangeContext('a', $a);
        $this->addExchangeContext('b', $b);

        $this->getExchangeContext('a')->shouldReturn($a);
        $this->getExchangeContext('b')->shouldReturn($b);
    }
}
