<?php

namespace Evaneos\Hector\Exchange;

use Evaneos\Hector\Channel\Channel;
use Evaneos\Hector\Context\ContextRegistry;

class ExchangeFactory
{
    /** @var  ContextRegistry */
    private $contextRegistry;

    /** @var  ExchangeRegistry */
    private $exchangeRegistry;

    /**
     * ExchangeFactory constructor.
     *
     * @param ContextRegistry  $contextRegistry
     * @param ExchangeRegistry $exchangeRegistry
     */
    public function __construct(ContextRegistry $contextRegistry, ExchangeRegistry $exchangeRegistry)
    {
        $this->contextRegistry  = $contextRegistry;
        $this->exchangeRegistry = $exchangeRegistry;
    }

    /**
     * @param string  $name
     * @param Channel $channel
     *
     * @return Exchange
     */
    public function createNamed($name, Channel $channel)
    {
        $exchange = new Exchange($name, $channel, $this->contextRegistry->getExchangeContext($name));
        $this->exchangeRegistry->addExchange($exchange);

        return $exchange;
    }
}
