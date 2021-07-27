<?php


namespace Miniyus\RestfulApiClient\Api\EndPoint;

use Illuminate\Support\Str;
use Miniyus\RestfulApiClient\Api\Api;
use Miniyus\RestfulApiClient\Api\Client;
use Miniyus\RestfulApiClient\Api\ConfigParser;
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
    public function __construct(string $host = null, string $server = 'default')
    {
        parent::__construct($host);

        if (!is_null($host)) {
            $this->setHost($host);
        }
    }

    /**
     * @param ConfigParser $config
     * @return $this
     */
    public function setConfig(ConfigParser $config): AbstractEndPoint
    {
        $this->config = $config;
        return $this;
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
        $class = $this->config('module_namespace') . "\\" . Str::studly($this->endPoint()) . "\\Resource\\" . Str::studly($name);

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
