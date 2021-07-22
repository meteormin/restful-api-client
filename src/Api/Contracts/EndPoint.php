<?php


namespace Miniyus\RestfulApiClient\Api\Contracts;


use Miniyus\RestfulApiClient\Api\ConfigParser;

interface EndPoint
{
    /**
     * @return string
     */
    public function endPoint(): string;

    /**
     * @param ConfigParser $config
     * @return $this
     */
    public function setConfig(ConfigParser $config): EndPoint;
}
