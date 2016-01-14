<?php

namespace spec\Evaneos\Hector\Connection;

use Assert\Assertion;
use Evaneos\Hector\Connection\Connection;
use Evaneos\Hector\Connection\ConnectionFactory;
use Evaneos\Hector\Connection\ConnectionRegistry;
use Evaneos\Hector\Exception\HectorException;
use PhpSpec\ObjectBehavior;

class ConnectionFactorySpec extends ObjectBehavior
{
    public function let(ConnectionRegistry $registry)
    {
        $configs = [
            'default' => [
                'vhost' => 'foo',
            ],
        ];

        $this->beConstructedWith($registry, $configs);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(ConnectionFactory::class);
    }

    public function it_should_create_configured_connection(ConnectionRegistry $registry)
    {
        /** @var Connection $connection */
        $connection = $this->createNamed('default')->shouldReturnAnInstanceOf(Connection::class);
        $registry->addConnection($connection)->shouldHaveBeenCalled();
        Assertion::eq($connection->getWrappedConnection()->getVhost(), 'foo');
    }

    public function it_should_throw_exception_if_bad_connection_name()
    {
        $this->shouldThrow(HectorException::class)->during('createNamed', ['foo']);
    }
}
