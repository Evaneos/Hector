<?php

namespace Evaneos\Hector\Context;

use Evaneos\Hector\Exchange\Context as ExchangeContext;
use Evaneos\Hector\Queue\Context as QueueContext;

class ContextRegistry
{
    /** @var QueueContext[] */
    private $queues;

    /** @var ExchangeContext[] */
    private $exchanges;

    public function __construct()
    {
        $this->queues    = [];
        $this->exchanges = [];
    }

    /**
     * @param string       $name
     * @param QueueContext $context
     */
    public function addQueueContext($name, QueueContext $context)
    {
        $this->queues[$name] = $context;
    }

    /**
     * @param string          $name
     * @param ExchangeContext $context
     */
    public function addExchangeContext($name, ExchangeContext $context)
    {
        $this->exchanges[$name] = $context;
    }

    /**
     * @param string $name
     *
     * @return ExchangeContext
     */
    public function getExchangeContext($name)
    {
        return $this->exchanges[$name];
    }

    /**
     * @param string $name
     *
     * @return QueueContext
     */
    public function getQueueContext($name)
    {
        return $this->queues[$name];
    }
}
