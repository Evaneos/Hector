<?php

namespace spec\Evaneos\Hector\Exchange;

use Evaneos\Hector\Channel\Channel;
use Evaneos\Hector\Exception\NotFoundException;
use Evaneos\Hector\Exchange\Exchange;
use Evaneos\Hector\Exchange\ExchangeRegistry;
use PhpSpec\ObjectBehavior;

class ExchangeRegistrySpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(ExchangeRegistry::class);
    }

    public function it_should_add_exchange(
        Exchange $a,
        Channel $channel
    ) {
        $a->getFingerPrint()->willReturn('a');
        $this->addExchange($a);
        $a->isEqual('a', $channel)->willReturn(true);

        $this->getExchange('a', $channel)->shouldReturn($a);
    }

    public function it_should_give_exchange(
        Exchange $a,
        Exchange $b,
        Channel $channel
    ) {
        $a->getFingerPrint()->willReturn('a');
        $b->getFingerPrint()->willReturn('b');

        $this->addExchange($a);
        $this->addExchange($b);

        $a->isEqual('b', $channel)->willReturn(false);
        $a->isEqual('a', $channel)->willReturn(true);

        $b->isEqual('b', $channel)->willReturn(true);
        $b->isEqual('a', $channel)->willReturn(false);

        $this->getExchange('b', $channel)->shouldReturn($b);
        $this->getExchange('a', $channel)->shouldReturn($a);
    }

    public function it_should_throw_exception_when_not_found(Channel $channel)
    {
        $channel->getIdentity()->willReturn('foo');

        $this->shouldThrow(new NotFoundException('Unable to find exchange bar for channel foo'))
            ->during('getExchange', ['bar', $channel])
        ;
    }

    public function it_should_check_if_contain_exchange(
        Exchange $a,
        Exchange $b,
        Channel $channel
    ) {
        $a->getFingerPrint()->willReturn('a');
        $b->getFingerPrint()->willReturn('b');

        $this->addExchange($a);
        $this->addExchange($b);

        $a->isEqual('b', $channel)->willReturn(false);
        $a->isEqual('a', $channel)->willReturn(true);
        $a->isEqual('c', $channel)->willReturn(false);

        $b->isEqual('b', $channel)->willReturn(true);
        $b->isEqual('a', $channel)->willReturn(false);
        $b->isEqual('c', $channel)->willReturn(false);

        $this->hasExchange('a', $channel)->shouldReturn(true);
        $this->hasExchange('b', $channel)->shouldReturn(true);
        $this->hasExchange('c', $channel)->shouldReturn(false);
    }
}
