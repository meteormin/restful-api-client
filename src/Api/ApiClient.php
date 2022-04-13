<?php


namespace Miniyus\RestfulApiClient\Api;


use Miniyus\RestfulApiClient\Api\Contracts\EndPoint;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Str;

/**
 * Class ApiClient
 * @package Miniyus\RestfulApiClient\Api
 */
abstract class ApiClient extends Client
{
    use Api;

    /**
     * @var string|null
     */
    protected ?string $host;

    /**
     * response
     * @var Response|null
     */
    protected ?Response $response;

    /**
     * @var string
     */
    protected string $type;

    /**
     * @var string|null
     */
    protected ?string $server;

    protected string $configName = 'api_server';

    /**
     * @var array<string,string>
     */
    protected array $modules = [];

    /**
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
     * @return EndPoint|null
     */
    public static function __callStatic($name, $arguments)
    {
        $static = static::newInstance();

        return $static->$name();
    }

    /**
     * @param $name
     * @param $arguments
     * @return EndPoint|null
     */
    public function __call($name, $arguments)
    {
        return $this->makeEndPoint($name);
    }

    /**
     * @param string|null $host
     * @return ApiClient
     */
    public static function newInstance(string $host = null): ApiClient
    {
        return parent::newInstance($host);
    }

    /**
     * @param string $name
     * @return string|null
     */
    protected function getEndPointClass(string $name): ?string
    {
        if (count($this->modules) == 0) {
            $apis = $this->config('end_point');
        } else {
            $apis = $this->modules[$name];
        }

        $className = null;

        $classNames = array_filter($apis, function ($value, $key) use ($name) {
            return $name == $key;
        });

        if (is_string($classNames[0])) {
            $className = Str::studly($classNames[0]);
        }

        return $className;
    }

    /**
     * @param string $name
     * @return EndPoint|null
     */
    protected function makeEndPoint(string $name): ?EndPoint
    {
        $class = $this->getEndPointClass($name);
        if (is_null($class)) {
            return null;
        }

        if (class_exists($class)) {
            $object = new $class($this->host, $this->type, $this->server ?? $this->config->all());
            if ($object instanceof EndPoint) {

                $object->url = $this->getHost() . '/' . $object->endPoint();

                return $object;
            }
        }

        return null;
    }

    /**
     * @return string
     */
    abstract public function endPoint(): string;

}
