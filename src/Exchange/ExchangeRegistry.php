<?php

namespace Evaneos\Hector\Exchange;

use Evaneos\Hector\Channel\Channel;
use Evaneos\Hector\Exception\NotFoundException;

class ExchangeRegistry
{
    /**
     * @var Exchange[]
     */
    private $exchanges;

    public function __construct()
    {
        $this->exchanges = [];
    }

    /**
     * @param Exchange $exchange
     */
    public function addExchange(Exchange $exchange)
    {
        $this->exchanges[$exchange->getFingerPrint()] = $exchange;
    }

    /**
     * @param string  $name
     * @param Channel $channel
     *
     * @return bool
     */
    public function hasExchange($name, Channel $channel)
    {
        /*
         * @var string
         * @var Exchange
         */
        foreach ($this->exchanges as $exchange) {
            if (true == $exchange->isEqual($name, $channel)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string  $name
     * @param Channel $channel
     *
     * @throws \Exception
     *
     * @return Exchange
     */
    public function getExchange($name, Channel $channel)
    {
        /*
         * @var string
         * @var Exchange
         */
        foreach ($this->exchanges as $exchange) {
            if ($exchange->isEqual($name, $channel)) {
                return $exchange;
            }
        }

        throw new NotFoundException(sprintf(
            'Unable to find exchange %s for channel %s',
            $name,
            $channel->getIdentity()
        ));
    }
}
