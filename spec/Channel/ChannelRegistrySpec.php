<?php

namespace spec\Evaneos\Hector\Channel;

use Evaneos\Hector\Channel\Channel;
use Evaneos\Hector\Channel\ChannelRegistry;
use PhpSpec\ObjectBehavior;

class ChannelRegistrySpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(ChannelRegistry::class);
    }

    public function it_should_add_channel(Channel $a, Channel $b)
    {
        $a->getIdentity()->willReturn('a');
        $b->getIdentity()->willReturn('b');

        $this->addChannel($a);
        $this->addChannel($b);

        $this->getChannel('a')->shouldReturn($a);
        $this->getChannel('b')->shouldReturn($b);
    }
}
