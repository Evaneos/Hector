<?php

namespace spec\Evaneos\Hector\Publisher;

use Evaneos\Hector\Channel\Channel;
use Evaneos\Hector\Connection\Connection;
use Evaneos\Hector\Events\PublisherEvent;
use Evaneos\Hector\Events\PublisherEvents;
use Evaneos\Hector\Events\SuccessPublisherEvent;
use Evaneos\Hector\Exchange\Exchange;
use Evaneos\Hector\Identity\Identity;
use Evaneos\Hector\Publisher\Publisher;
use PhpSpec\ObjectBehavior;
use Symfony\Component\EventDispatcher\EventDispatcher;

class PublisherSpec extends ObjectBehavior
{
    public function let(
        Identity $identity,
        EventDispatcher $eventDispatcher,
        Connection $connection,
        Channel $channel,
        Exchange $exchange
    ) {
        $this->beConstructedWith(
            $identity,
            $eventDispatcher,
            $connection,
            $channel,
            $exchange,
            []
        );
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(Publisher::class);
    }

    public function it_should_initialize(
        Connection $connection,
        Channel $channel,
        Exchange $exchange
    ) {
        $connection->connect()->shouldBeCalled();
        $channel->isInitialized()->willReturn(false);
        $exchange->isInitialized()->willReturn(false);

        $channel->initialize()->shouldBeCalled();
        $exchange->initialize()->shouldBeCalled();

        $this->initialize();

        $this->isInitialized()->shouldReturn(true);
    }

    public function it_should_not_be_initialized()
    {
        $this->isInitialized()->shouldReturn(false);
    }

    public function it_should_give_channel(Channel $channel)
    {
        $this->getChannel()->shouldReturn($channel);
    }

    public function it_should_give_connection(Connection $connection)
    {
        $this->getConnection()->shouldReturn($connection);
    }

    public function it_should_give_exchange(Exchange $exchange)
    {
        $this->getExchange()->shouldReturn($exchange);
    }

    public function it_should_give_identity(Identity $identity)
    {
        $identity->getIdentifier()->willReturn('foo');
        $this->getIdentity()->shouldReturn('foo');
    }

    public function it_should_start_transaction(Channel $channel)
    {
        $channel->startTransaction()->shouldBeCalled();

        $this->startTransaction();
    }

    public function it_should_commit_transaction(Channel $channel)
    {
        $channel->commitTransaction()->shouldBeCalled();

        $this->commitTransaction();
    }

    public function it_should_rollback_transaction(Channel $channel)
    {
        $channel->rollbackTransaction()->shouldBeCalled();

        $this->rollbackTransaction();
    }

    public function it_should_handle_transaction(Channel $channel)
    {
        $stuff = function (Channel $channel) { };

        $channel->transaction($stuff)->shouldBeCalled();

        $this->transaction($stuff);
    }

    public function it_should_publish_message(
        EventDispatcher $eventDispatcher,
        Exchange $exchange,
        Channel $channel,
        \AMQPExchange $AMQPExchange,
        Connection $connection
    ) {
        $message    = 'foo.bar';
        $routingKey = 'baz';
        $flags      = AMQP_NOPARAM;
        $attributes = [
            'x-expires' => 1000,
        ];

        $event = new PublisherEvent(
            $message,
            $routingKey,
            $flags,
            $attributes,
            $exchange->getWrappedObject()
        );

        $successEvent = new SuccessPublisherEvent($event);

        $connection->connect()->shouldBeCalled();
        $channel->isInitialized()->willReturn(true);
        $exchange->isInitialized()->willReturn(true);

        $eventDispatcher->dispatch(PublisherEvents::PRE_PUBLISH, $event)->shouldBeCalled();
        $AMQPExchange->publish($message, $routingKey, $flags, $attributes)->willReturn(true);
        $exchange->getWrappedExchange()->willReturn($AMQPExchange);
        $eventDispatcher->dispatch(PublisherEvents::SUCCESS_PUBLISH, $successEvent)->shouldBeCalled();

        $this->publish($message, $routingKey, $flags, $attributes, false)->shouldReturn(true);
    }


    public function it_should_publish_message_with_routing_key_prefixed(
        EventDispatcher $eventDispatcher,
        Exchange $exchange,
        Channel $channel,
        \AMQPExchange $AMQPExchange,
        Connection $connection,
        Identity $identity
    ) {
        $prefixRoutingKey = 'foo.';
        $this->beConstructedWith(
            $identity,
            $eventDispatcher,
            $connection,
            $channel,
            $exchange,
            [
                'routing_key_prefix' => $prefixRoutingKey
            ]
        );

        $message    = 'foo.bar';
        $routingKey = 'baz';
        $flags      = AMQP_NOPARAM;
        $attributes = [
            'x-expires' => 1000,
        ];

        $finalRoutingKey = "foo." . $routingKey;
        $event = new PublisherEvent(
            $message,
            $finalRoutingKey,
            $flags,
            $attributes,
            $exchange->getWrappedObject()
        );

        $successEvent = new SuccessPublisherEvent($event);

        $connection->connect()->shouldBeCalled();
        $channel->isInitialized()->willReturn(true);
        $exchange->isInitialized()->willReturn(true);

        $eventDispatcher->dispatch(PublisherEvents::PRE_PUBLISH, $event)->shouldBeCalled();
        $AMQPExchange->publish($message, $finalRoutingKey, $flags, $attributes)->willReturn(true);
        $exchange->getWrappedExchange()->willReturn($AMQPExchange);
        $eventDispatcher->dispatch(PublisherEvents::SUCCESS_PUBLISH, $successEvent)->shouldBeCalled();

        $this->publish($message, $routingKey, $flags, $attributes, false)->shouldReturn(true);
    }

    public function it_should_publish_message_with_routing_key_null(
        EventDispatcher $eventDispatcher,
        Exchange $exchange,
        Channel $channel,
        \AMQPExchange $AMQPExchange,
        Connection $connection,
        Identity $identity
    ) {

        $message    = 'foo.bar';
        $routingKey = null;
        $flags      = AMQP_NOPARAM;
        $attributes = [
            'x-expires' => 1000,
        ];

        $event = new PublisherEvent(
            $message,
            $routingKey,
            $flags,
            $attributes,
            $exchange->getWrappedObject()
        );

        $successEvent = new SuccessPublisherEvent($event);

        $connection->connect()->shouldBeCalled();
        $channel->isInitialized()->willReturn(true);
        $exchange->isInitialized()->willReturn(true);

        $eventDispatcher->dispatch(PublisherEvents::PRE_PUBLISH, $event)->shouldBeCalled();
        $AMQPExchange->publish($message, $routingKey, $flags, $attributes)->willReturn(true);
        $exchange->getWrappedExchange()->willReturn($AMQPExchange);
        $eventDispatcher->dispatch(PublisherEvents::SUCCESS_PUBLISH, $successEvent)->shouldBeCalled();

        $this->publish($message, $routingKey, $flags, $attributes, false)->shouldReturn(true);
    }

    public function it_should_publish_message_with_routing_key_prefix_only(
        EventDispatcher $eventDispatcher,
        Exchange $exchange,
        Channel $channel,
        \AMQPExchange $AMQPExchange,
        Connection $connection,
        Identity $identity
    ) {
        $prefixRoutingKey = 'foo.';
        $this->beConstructedWith(
            $identity,
            $eventDispatcher,
            $connection,
            $channel,
            $exchange,
            [
                'routing_key_prefix' => $prefixRoutingKey
            ]
        );

        $message    = 'foo.bar';
        $routingKey = null;
        $flags      = AMQP_NOPARAM;
        $attributes = [
            'x-expires' => 1000,
        ];

        $event = new PublisherEvent(
            $message,
            $prefixRoutingKey,
            $flags,
            $attributes,
            $exchange->getWrappedObject()
        );

        $successEvent = new SuccessPublisherEvent($event);

        $connection->connect()->shouldBeCalled();
        $channel->isInitialized()->willReturn(true);
        $exchange->isInitialized()->willReturn(true);

        $eventDispatcher->dispatch(PublisherEvents::PRE_PUBLISH, $event)->shouldBeCalled();
        $AMQPExchange->publish($message, $prefixRoutingKey, $flags, $attributes)->willReturn(true);
        $exchange->getWrappedExchange()->willReturn($AMQPExchange);
        $eventDispatcher->dispatch(PublisherEvents::SUCCESS_PUBLISH, $successEvent)->shouldBeCalled();

        $this->publish($message, $routingKey, $flags, $attributes, false)->shouldReturn(true);
    }
}
