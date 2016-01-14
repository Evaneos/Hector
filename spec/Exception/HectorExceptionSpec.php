<?php

namespace spec\Evaneos\Hector\Exception;

use Evaneos\Hector\Exception\HectorException;
use PhpSpec\ObjectBehavior;

class HectorExceptionSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(HectorException::class);
    }
}
