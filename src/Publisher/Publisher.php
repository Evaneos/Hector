<?php

namespace Evaneos\Hector\Publisher;

use Evaneos\Hector\Channel\Channel;
use Evaneos\Hector\Connection\Connection;
use Evaneos\Hector\Events\FailedPublisherEvent;
use Evaneos\Hector\Events\PublisherEvent;
use Evaneos\Hector\Events\PublisherEvents;
use Evaneos\Hector\Events\SuccessPublisherEvent;
use Evaneos\Hector\Exchange\Exchange;
use Evaneos\Hector\Identity\Identity;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Publisher
{
    /** @var Connection  */
    private $connection;

    /** @var EventDispatcherInterface  */
    private $eventDispatcher;

    /** @var Channel  */
    private $channel;

    /** @var Exchange  */
    private $exchange;

    /** @var Identity  */
    private $identity;

    /** @var string  */
    private $routingKeyPrefix;

    /** @var  bool */
    private $initialized;

    /**
     * Publisher constructor.
     *
     * @param Identity                 $identity
     * @param EventDispatcherInterface $eventDispatcher
     * @param Connection               $connection
     * @param Channel                  $channel
     * @param Exchange                 $exchange
     * @param array                    $otpions
     */
    public function __construct(
        Identity $identity,
        EventDispatcherInterface $eventDispatcher = null,
        Connection $connection,
        Channel $channel,
        Exchange $exchange,
        array $otpions = []
    ) {
        $this->connection      = $connection;
        $this->eventDispatcher = $eventDispatcher;
        $this->identity        = $identity;
        $this->channel         = $channel;
        $this->exchange        = $exchange;
        $this->initialized     = false;

        if (isset($options['routing_key_prefix'])) {
            $this->routingKeyPrefix = $options['routing_key_prefix'];
        }
    }

    public function initialize()
    {
        $this->connection->connect();

        if (false === $this->channel->isInitialized()) {
            $this->channel->initialize();
        }

        if (false === $this->exchange->isInitialized()) {
            $this->exchange->initialize();
        }

        $this->initialized = true;
    }

    /**
     * @return Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @return Exchange
     */
    public function getExchange()
    {
        return $this->exchange;
    }

    /**
     * @return Channel
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @return string
     */
    public function getIdentity()
    {
        return $this->identity->getIdentifier();
    }

    /**
     * @return bool
     */
    public function startTransaction()
    {
        return $this->channel->startTransaction();
    }

    /**
     * @return bool
     */
    public function commitTransaction()
    {
        return $this->channel->commitTransaction();
    }

    /**
     * @return bool
     */
    public function rollbackTransaction()
    {
        return $this->channel->rollbackTransaction();
    }

    /**
     * @param \Closure $closure
     *
     * @throws \Evaneos\Hector\Exception\HectorException
     *
     * @return bool
     */
    public function transaction(\Closure $closure)
    {
        return $this->channel->transaction($closure);
    }

    /**
     * @return bool
     */
    public function isInitialized()
    {
        return $this->initialized;
    }

    /**
     * @param string $message
     * @param null   $routingKeyArgument::type(Publisher::class)
     * @param int    $flags
     * @param array  $attributes
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function publish($message, $routingKey = null, $flags = AMQP_NOPARAM, array $attributes = [])
    {
        $hasEventDispatcher = null !== $this->eventDispatcher;

        if (!$this->isInitialized()) {
            $this->initialize();
        }

        if(true === $hasEventDispatcher) {
            $event = new PublisherEvent(
                $message,
                $routingKey,
                $flags,
                $attributes,
                $this->exchange
            );
        }

        try {
            if(true === $hasEventDispatcher){
                $this->eventDispatcher->dispatch(PublisherEvents::PRE_PUBLISH, $event);

                $result = $this->exchange->getWrappedExchange()->publish(
                    $event->getMessage(),
                    $event->getRoutingKey(),
                    $event->getFlags(),
                    $event->getAttributes()
                );
            }else{
                $result = $this->exchange->getWrappedExchange()->publish(
                    $message,
                    $routingKey,
                    $flags,
                    $attributes
                );
            }

            if (!$result) {
                if(true === $hasEventDispatcher){
                    $this->eventDispatcher->dispatch(PublisherEvents::FAIL_PUBLISH, new FailedPublisherEvent($event, null, $this));
                }
            } else {
                if(true === $hasEventDispatcher){
                    $this->eventDispatcher->dispatch(PublisherEvents::SUCCESS_PUBLISH, new SuccessPublisherEvent($event));
                }
            }
        } catch (\Exception $e) {
            if(true === $hasEventDispatcher) {
                $this->eventDispatcher->dispatch(PublisherEvents::FAIL_PUBLISH, new FailedPublisherEvent($event, $e, $this));
            }

            throw $e;
        }

        return $result;
    }
}
