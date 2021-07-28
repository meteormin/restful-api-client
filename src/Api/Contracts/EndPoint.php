<?php


namespace Miniyus\RestfulApiClient\Api\Contracts;


use Miniyus\RestfulApiClient\Api\ConfigParser;

interface EndPoint
{
    /**
     * @return string
     */
    public function endPoint(): string;
}
