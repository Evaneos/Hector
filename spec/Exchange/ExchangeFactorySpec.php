<?php

namespace spec\Evaneos\Hector\Exchange;

use Evaneos\Hector\Channel\Channel;
use Evaneos\Hector\Context\ContextRegistry;
use Evaneos\Hector\Exchange\Context;
use Evaneos\Hector\Exchange\ExchangeFactory;
use Evaneos\Hector\Exchange\ExchangeRegistry;
use PhpSpec\ObjectBehavior;

class ExchangeFactorySpec extends ObjectBehavior
{
    public function let(ContextRegistry $contextRegistry, ExchangeRegistry $exchangeRegistry)
    {
        $this->beConstructedWith($contextRegistry, $exchangeRegistry);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(ExchangeFactory::class);
    }

    public function it_should_create_exchange(
        Channel $channel,
        ExchangeRegistry $exchangeRegistry,
        ContextRegistry $contextRegistry,
        Context $context
    ) {
        $contextRegistry->getExchangeContext('exchange')->willReturn($context);
        $exchange = $this->createNamed('exchange', $channel);
        $exchangeRegistry->addExchange($exchange)->shouldHaveBeenCalled();
    }
}
