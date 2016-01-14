<?php

use Evaneos\Hector\Channel\ChannelFactory;
use Evaneos\Hector\Channel\ChannelRegistry;
use Evaneos\Hector\Connection\ConnectionFactory;
use Evaneos\Hector\Connection\ConnectionRegistry;
use Evaneos\Hector\Consumer\ConsumerFactory;
use Evaneos\Hector\Context\ContextRegistry;
use Evaneos\Hector\Exchange\Context;
use Evaneos\Hector\Exchange\ExchangeFactory;
use Evaneos\Hector\Exchange\ExchangeRegistry;
use Evaneos\Hector\Identity\Identity;
use Evaneos\Hector\Queue\QueueFactory;
use Evaneos\Hector\Queue\QueueRegistry;

require __DIR__.'/vendor/autoload.php';

$connectionRegistry = new ConnectionRegistry();
$channelRegistry = new ChannelRegistry();
$exchangeRegistry = new ExchangeRegistry();
$queueRegistry = new QueueRegistry();
$contextRegistry = new ContextRegistry();

$connectionFactory = new ConnectionFactory($connectionRegistry, [
    'default' => [
        'host' => 'rabbitmq',
        'port' => 5672,
        'login' => 'admin',
        'password' => 'admin',
        'vhost' => '/',
        'read_timeout' => 0.5,
        'write_timeout' => 0.5,
        'conntect_timeout' => 0.5
    ]
]);

$channelFactory = new ChannelFactory($connectionRegistry, $channelRegistry, $channelFactory);

$connection = $connectionFactory->createNamed('default');

$contextRegistry->addExchangeContext('exchange_a', new Context(\AMQP_DURABLE));
$contextRegistry->addExchangeContext('exchange_b', new Context(\AMQP_DURABLE));
$contextRegistry->addExchangeContext('exchange_c', new Context(\AMQP_DURABLE));

$contextRegistry->addQueueContext('queue_a_foo', new \Evaneos\Hector\Queue\Context('a.foo'));
$contextRegistry->addQueueContext('queue_a_bar', new \Evaneos\Hector\Queue\Context('a.bar'));
$contextRegistry->addQueueContext('queue_b', new \Evaneos\Hector\Queue\Context());

$exchangeFactory = new ExchangeFactory($contextRegistry, $exchangeRegistry);

// A channel is a virtual connection to insulate work process inside her.
// Our worker will process across 3 queues from different exchange, all communication between the broker and worker will be through this channel
$channel = $channelFactory->createFromConnectionName('default', new Identity());

$exchangeA = $exchangeFactory->createNamed('exchange_a', $channel);
$exchangeB = $exchangeFactory->createNamed('exchange_b', $channel);

$queueFactory = new QueueFactory($contextRegistry, $queueRegistry);

/**
 * exchange_a is bind to queue_a_foo and queue_a_bar and communicate over the same channel
 *
 * If you have a worker who consume from multiple queue via get() with an event loop, you should aggregate in only one channel
 */
$queueAFoo = $queueFactory->createNamed('queue_a_foo', $channel, $exchangeA);
$queueABar = $queueFactory->createNamed('queue_a_bar', $channel, $exchangeA);
$queueB = $queueFactory->createNamed('queue_b', $channel, $exchangeB);

$consumerFactory = new ConsumerFactory($channelFactory, $queueFactory, $exchangeFactory, $connectionRegistry, $exchangeRegistry, $queueRegistry);

//all of these consumer work through the same channel
$consumerAFoo = $consumerFactory->create('default', 'exchange_a', 'queue_a_foo');
$consumerABar = $consumerFactory->create('default', 'exchange_a', 'queue_a_bar');
$consumerB = $consumerFactory->create('default', 'exchange_b', 'queue_b');

//$consumerB->getQueue()->getWrappedQueue()->get())
//$consumerB->getQueue()->getWrappedQueue()->consume())

