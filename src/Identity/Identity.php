<?php

namespace Evaneos\Hector\Identity;

use Ramsey\Uuid\Uuid;

class Identity
{
    /** @var  string */
    private $identifier;

    public function __construct()
    {
        $this->identifier = (string) Uuid::uuid4();
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }
}
