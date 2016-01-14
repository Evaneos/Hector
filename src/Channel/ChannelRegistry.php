<?php

namespace Evaneos\Hector\Channel;

class ChannelRegistry
{
    /** @var Channel[]  */
    private $channels;

    public function __construct()
    {
        $this->channels = [];
    }

    /**
     * @param Channel $channel
     */
    public function addChannel(Channel $channel)
    {
        $this->channels[$channel->getIdentity()] = $channel;
    }

    /**
     * @param string $identity
     *
     * @return Channel
     */
    public function getChannel($identity)
    {
        return $this->channels[$identity];
    }
}
