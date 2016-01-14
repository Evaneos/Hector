<?php

namespace spec\Evaneos\Hector\Connection;

use Evaneos\Hector\Connection\Connection;
use PhpSpec\ObjectBehavior;

class ConnectionRegistrySpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Evaneos\Hector\Connection\ConnectionRegistry');
    }

    public function it_should_give_specified_connection(Connection $connectionA, Connection $connectionB)
    {
        $connectionA->getName()->willReturn('foo');
        $connectionB->getName()->willReturn('bar');

        $this->addConnection($connectionA);
        $this->addConnection($connectionB);

        $this->getConnection('foo')->shouldBeLike($connectionA);
        $this->getConnection('bar')->shouldBeLike($connectionB);
    }
}
