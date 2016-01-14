<?php

namespace Evaneos\Hector\Queue;

use Evaneos\Hector\Channel\Channel;
use Evaneos\Hector\Exception\NotFoundException;
use Evaneos\Hector\Exchange\Exchange;

class QueueRegistry
{
    /**
     * @var Queue[]
     */
    private $queues;

    public function __construct()
    {
        $this->queues = [];
    }

    /**
     * @param Queue $queue
     */
    public function addQueue(Queue $queue)
    {
        $this->queues[$queue->getFingerPrint()] = $queue;
    }

    /**
     * @param string   $name
     * @param Channel  $channel
     * @param Exchange $exchange
     *
     * @return bool
     */
    public function hasQueue($name, Channel $channel, Exchange $exchange)
    {
        foreach ($this->queues as $fingerPrint => $queue) {
            if ($queue->isEqual($name, $channel, $exchange)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string   $name
     * @param Channel  $channel
     * @param Exchange $exchange
     *
     * @throws \Exception
     *
     * @return Queue
     */
    public function getQueue($name, Channel $channel, Exchange $exchange)
    {
        /*
         * @var string
         * @var Queue
         */
        foreach ($this->queues as $fingerPrint => $queue) {
            if ($queue->isEqual($name, $channel, $exchange)) {
                return $queue;
            }
        }

        throw new NotFoundException(sprintf(
            'Unable to find queue %s for channel %s and exchange %s',
            $name,
            $channel->getIdentity(),
            $exchange->getName()
        ));
    }

    /**
     * @param $name
     *
     * @throws NotFoundException
     *
     * @return Context
     */
    public function getQueueContext($name)
    {
        /*
         * @var string
         * @var Queue
         */
        foreach ($this->queues as $fingerPrint => $queue) {
            if ($queue->getName() === $name) {
                return $queue->getContext();
            }
        }

        throw new NotFoundException(sprintf(
            'Unable to find queue %s',
            $name
        ));
    }
}
