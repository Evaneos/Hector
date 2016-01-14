<?php

use Evaneos\Hector\Channel\Channel;
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

$contextRegistry->addExchangeContext('exchange_a', new Context(\AMQP_EX_TYPE_DIRECT));
$contextRegistry->addExchangeContext('exchange_b', new Context(\AMQP_EX_TYPE_DIRECT));

$exchangeFactory = new ExchangeFactory($contextRegistry, $exchangeRegistry);

$channelA = $channelFactory->createFromConnectionName('default', new Identity());
$channelB = $channelFactory->createFromConnectionName('default', new Identity());

$exchange = $exchangeFactory->createNamed('exchange_a', $channelA);
$exchange = $exchangeFactory->createNamed('exchange_b', $channelB);

$publisherFactory = new PublisherFactory($connectionRegistry, $exchangeFactory, $channelFactory, null, $exchangeRegistry);

$publisherA = $publisherFactory->create('default', 'exchange_a');
$publisherB = $publisherFactory->create('default', 'exchange_b');

//simple publish to a
$publisherA->publish('Hello world', 'routing_key');

//simple publish to b
$publisherB->publish('Hello world', 'routing_key');

//multi publish with the same publisher
try{
    //PublisherA is connected with ChannelA, everything happened in channelA will be rollback if something wrong happen
    $publisherA->startTransaction();
    $publisherA->publish('Hello world', 'routing_key', AMQP_NOPARAM, []);
    $publisherA->publish('Hello world', 'routing_key', AMQP_NOPARAM, []);
    $publisherA->publish('Hello world', 'routing_key', AMQP_NOPARAM, []);
    $publisherA->publish('Hello world', 'routing_key', AMQP_NOPARAM, []);
    $publisherA->publish('Hello world', 'routing_key', AMQP_NOPARAM, []);
    $publisherA->commitTransaction();
} catch(\Exception $e) {
    $publisherA->rollbackTransaction();
    throw $e;
}

//multi publish with across multi publisher

//publisherA use ChannelA
//publisherB use ChannelB
// It's good because you want avoid side effect when you commit or rollback but if you commit on both ?

$channelTransaction = $channelFactory->createFromConnectionName('default', new Identity());

//Now these 2 publisher communicate through the same channel and old stay insulated
$publisherATransaction = $publisherFactory->create('default', 'exchange_a', $channelTransaction);
$publisherBTransaction = $publisherFactory->create('default', 'exchange_b', $channelTransaction);

//Another way to do transaction
$channelTransaction->transaction(function(Channel $channel) use ($publisherATransaction, $publisherBTransaction){
    $publisherATransaction->publish('Hello world');
    $publisherATransaction->publish('Hello world');
    $publisherATransaction->publish('Hello world');

    $publisherBTransaction->publish('Hello world');
    $publisherBTransaction->publish('Hello world');
    $publisherBTransaction->publish('Hello world');
});
