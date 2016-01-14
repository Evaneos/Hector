<?php

use Evaneos\Hector\Channel\ChannelFactory;
use Evaneos\Hector\Channel\ChannelRegistry;
use Evaneos\Hector\Connection\ConnectionFactory;
use Evaneos\Hector\Connection\ConnectionRegistry;
use Evaneos\Hector\Context\ContextRegistry;
use Evaneos\Hector\Exchange\Context;
use Evaneos\Hector\Exchange\ExchangeFactory;
use Evaneos\Hector\Exchange\ExchangeRegistry;
use Evaneos\Hector\Identity\Identity;
use Evaneos\Hector\Publisher\PublisherFactory;
use Evaneos\Hector\Queue\QueueFactory;
use Evaneos\Hector\Queue\QueueRegistry;
use Symfony\Component\EventDispatcher\EventDispatcher;

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

$contextRegistry->addExchangeContext('exchange_a', new Context(\AMQP_EX_TYPE_DIRECT));

$exchangeFactory = new ExchangeFactory($contextRegistry, $exchangeRegistry);

$channel = $channelFactory->createFromConnectionName('default', new Identity());
$exchange = $exchangeFactory->createNamed('exchange_a', $channel);

$publisherFactory = new PublisherFactory($connectionRegistry, $exchangeFactory, $channelFactory, null, $exchangeRegistry);

$publisher = $publisherFactory->create('default', 'exchange_a');

$publisher->publish('Hello world', 'routing_key');

