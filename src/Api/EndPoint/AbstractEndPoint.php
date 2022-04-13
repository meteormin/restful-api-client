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

    public string $url;

    /**
     * @var string
     */
    protected string $type;

    /**
     * @var string|null
     */
    protected ?string $server;

    /**
     * @var array
     */
    protected array $subClients = [];

    /**
     * AbstractEndPoint constructor.
     * @param string|null $host
     * @param string $type
     * @param array|string $server
     */
    public function __construct(string $host = null, string $type = 'storage', array|string $server = 'default')
    {
        parent::__construct($host);
        $this->initialize($host, $type, $server);
    }

    /**
     * @param $name
     * @param $arguments
     * @return AbstractSubClient
     */
    public function __call($name, $arguments)
    {
        return $this->makeClient($name);
    }

    /**
     * @param string $name
     * @return AbstractSubClient
     */
    protected function makeClient(string $name): AbstractSubClient
    {
        if (count($this->subClients) == 0) {
            $class = $this->config('module_namespace') . "\\" . Str::studly($this->endPoint()) . "\\Resource\\" . Str::studly($name);
        } else {
            $class = $this->subClients[$name];
        }

        /** @var AbstractSubClient $client */
        $client = new $class($this->host, $this->type, $this->server ?? $this->config->all());

        $namespace = $client->getNameSpace();
        $namePath = empty($namespace) ? $name : $namespace . '/' . $name;
        $url = $this->makeUrl($this->host, $namePath);
        $client->url = $url;
        $client->setEndPoint($this->endPoint());

        return $client;
    }

    /**
     * @return string
     */
    abstract public function endPoint(): string;

}
