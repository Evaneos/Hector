<?php

namespace spec\Evaneos\Hector\Identity;

use Evaneos\Hector\Identity\Identity;
use PhpSpec\ObjectBehavior;

class IdentitySpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(Identity::class);
    }

    public function it_should_give_identifier()
    {
        $this->getIdentifier()->shouldBeString();
    }
}
