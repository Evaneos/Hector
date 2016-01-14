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
use Evaneos\Hector\Publisher\PublisherFactory;
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

$contextRegistry->addExchangeContext('exchange_a', new Context(AMQP_EX_TYPE_DIRECT));
$contextRegistry->addExchangeContext('exchange_b', new Context(AMQP_EX_TYPE_DIRECT));
$contextRegistry->addExchangeContext('exchange_c', new Context(AMQP_EX_TYPE_DIRECT));

$contextRegistry->addQueueContext('queue_a_foo', new \Evaneos\Hector\Queue\Context('a.foo', AMQP_AUTOACK));
$contextRegistry->addQueueContext('queue_a_bar', new \Evaneos\Hector\Queue\Context('a.bar',  AMQP_AUTOACK));
$contextRegistry->addQueueContext('queue_b', new \Evaneos\Hector\Queue\Context('', AMQP_AUTOACK));

$exchangeFactory = new ExchangeFactory($contextRegistry, $exchangeRegistry);

$channelProcess1 = $channelFactory->createFromConnectionName('default', new Identity());
$channelProcess2 = $channelFactory->createFromConnectionName('default', new Identity());
$channelProcess3 = $channelFactory->createFromConnectionName('default', new Identity());

$exchangeAProcess1 = $exchangeFactory->createNamed('exchange_a', $channelProcess1);
$exchangeAProcess2 = $exchangeFactory->createNamed('exchange_a', $channelProcess2);
$exchangeBProcess3 = $exchangeFactory->createNamed('exchange_b', $channelProcess3);

$queueFactory = new QueueFactory($contextRegistry, $queueRegistry);

$consumerFactory = new ConsumerFactory($channelFactory, $queueFactory, $exchangeFactory, $connectionRegistry, $exchangeRegistry, $queueRegistry);

//all of these consumer work in different channel
$consumerAFoo = $consumerFactory->create('default', 'exchange_a', 'queue_a_foo');
$consumerABar = $consumerFactory->create('default', 'exchange_a', 'queue_a_bar');
$consumerB = $consumerFactory->create('default', 'exchange_b', 'queue_b');

//now publish some messages
$publisherFactory = new PublisherFactory($connectionRegistry, $exchangeFactory, $channelFactory, null, $exchangeRegistry);
$publisherAP1 = $publisherFactory->create('default', 'exchange_a', $channelProcess1);
$publisherAP2 = $publisherFactory->create('default', 'exchange_a', $channelProcess2);
$publisherBP3 = $publisherFactory->create('default', 'exchange_b', $channelProcess3);

$publisherAP1->publish('Hello from Publisher exchange_a process 1', 'a.foo');
$publisherAP2->publish('Hello from Publisher exchange_a process 2', 'a.bar');
$publisherBP3->publish('Hello from Publisher exchange_3 process 3');

//Trigger connection
$consumerAFoo->initialize();
$consumerABar->initialize();
$consumerB->initialize();

/** @var AMQPEnvelope $enveloppe */
while($enveloppe = $consumerAFoo->getQueue()->getWrappedQueue()->get()){
    echo $enveloppe->getBody().PHP_EOL;
}

/** @var AMQPEnvelope $enveloppe */
while($enveloppe = $consumerABar->getQueue()->getWrappedQueue()->get()){
    echo $enveloppe->getBody().PHP_EOL;
}

/** @var AMQPEnvelope $enveloppe */
while($enveloppe = $consumerB->getQueue()->getWrappedQueue()->get()){
    echo $enveloppe->getBody().PHP_EOL;
}

//$consumerB->getQueue()->getWrappedQueue()->get())
//$consumerB->getQueue()->getWrappedQueue()->consume())
