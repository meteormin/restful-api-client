<?php


namespace Miniyus\RestfulApiClient\Api\EndPoint;

use Miniyus\RestfulApiClient\Api\Api;
use Miniyus\RestfulApiClient\Api\Client;
use Miniyus\RestfulApiClient\Api\Contracts\EndPoint;

/**
 * Class AbstractEndPoint
 * @package App\Libraries\Api\EndPoint
 */
abstract class AbstractEndPoint extends Client implements EndPoint
{
    use Api;

    /**
     * AbstractEndPoint constructor.
     * @param string|null $host
     */
    public function __construct(string $host = null)
    {
        parent::__construct(config('api_server.host', $host));
    }

    /**
     * @param $name
     * @param $arguments
     * @return AbstractSubClient
     */
    public function __call($name, $arguments)
    {
        return $this->makeClient($name, $arguments[0] ?? null);
    }

    /**
     * @return string
     */
    abstract public function endPoint(): string;

    /**
     * @param string $name
     * @param string|null $host
     * @return AbstractSubClient
     */
    protected function makeClient(string $name, string $host = null): AbstractSubClient
    {
        $class = config('api_server.module_namespace') . "\\" . ucfirst($this->endPoint()) . "\\Resource\\" . ucfirst($name);

        /** @var AbstractSubClient $client */
        $client = new $class($host);

        $namespace = $client->getNameSpace();
        $namePath = empty($namespace) ? $name : $namespace . '/' . $name;
        $url = $this->makeUrl($this->getHost(), $namePath);
        $client->url = $url;
        $client->setEndPoint($this->endPoint());

        return $client;
    }
}
