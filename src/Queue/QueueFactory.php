<?php

namespace Evaneos\Hector\Queue;

use Evaneos\Hector\Channel\Channel;
use Evaneos\Hector\Context\ContextRegistry;
use Evaneos\Hector\Exchange\Exchange;

class QueueFactory
{
    /**
     * @var ContextRegistry
     */
    private $contextRegistry;

    /**
     * @var QueueRegistry
     */
    private $registry;

    /**
     * QueueFactory constructor.
     *
     * @param ContextRegistry $contextRegistry
     * @param QueueRegistry   $registry
     */
    public function __construct(ContextRegistry $contextRegistry, QueueRegistry $registry)
    {
        $this->contextRegistry = $contextRegistry;
        $this->registry        = $registry;
    }

    /**
     * @param string   $name
     * @param Channel  $channel
     * @param Exchange $exchange
     *
     * @return Queue
     */
    public function createNamed($name, Channel $channel, Exchange $exchange)
    {
        $queue = new Queue($name, $channel, $exchange, $this->contextRegistry->getQueueContext($name));
        $this->registry->addQueue($queue);

        return $queue;
    }
}
