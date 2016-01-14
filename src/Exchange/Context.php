<?php

namespace Evaneos\Hector\Exchange;

class Context
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var int
     */
    private $flags;

    /**
     * @var array
     */
    private $arguments;

    /**
     * Context constructor.
     *
     * @param int   $type
     * @param int   $flags
     * @param array $arguments
     */
    public function __construct($type, $flags = null, array $arguments = [])
    {
        $this->type      = $type;
        $this->flags     = $flags;
        $this->arguments = $arguments;
    }

    /**
     * @param array $configs
     *
     * @return Context
     */
    public static function createFromConfig(array $configs)
    {
        return new self(
            $configs['type'],
            $configs['flags'],
            $configs['arguments']
        );
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return int
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
}
