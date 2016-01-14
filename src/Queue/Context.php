<?php

namespace Evaneos\Hector\Queue;

class Context
{
    /** @var null|int  */
    private $flags;

    /** @var array  */
    private $arguments;

    /** @var  string */
    private $routingKey;

    /**
     * Context constructor.
     *
     * @param null  $flags
     * @param array $arguments
     */
    public function __construct($routingKey = '', $flags = null, array $arguments = [])
    {
        $this->flags      = $flags;
        $this->arguments  = $arguments;
        $this->routingKey = $routingKey;
    }

    /**
     * @return string
     */
    public function getRoutingKey()
    {
        return $this->routingKey;
    }

    /**
     */
    public function getFlags()
    {
        return $this->flags;
    }

    /**
     * @return array
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * @param array $configs
     *
     * @return Context
     */
    public static function createFromConfig(array $configs)
    {
        return new self(
            $configs['routing_key'],
            $configs['flags'],
            $configs['arguments']
        );
    }
}
