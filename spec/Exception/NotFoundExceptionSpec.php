<?php

namespace spec\Evaneos\Hector\Exception;

use Evaneos\Hector\Exception\NotFoundException;
use PhpSpec\ObjectBehavior;

class NotFoundExceptionSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(NotFoundException::class);
    }
}
