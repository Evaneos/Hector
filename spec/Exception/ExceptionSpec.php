<?php

namespace spec\Evaneos\Hector\Exception;

use Evaneos\Hector\Exception\NotFoundExceptionSpec;
use PhpSpec\ObjectBehavior;

class NotFoundExceptionSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(NotFoundExceptionSpec::class);
    }
}
