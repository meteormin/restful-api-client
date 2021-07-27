<?php


namespace Miniyus\RestfulApiClient\Api;


use Miniyus\RestfulApiClient\Api\Contracts\EndPoint;
use Miniyus\RestfulApiClient\Response\ErrorResponse;
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
     * response
     * @var Response|null
     */
    protected ?Response $response;

    /**
     * @var string|null
     */
    protected ?string $type;

    protected ?string $server;

    /**
     * ApiClient constructor.
     */
    public function __construct(string $host = null, string $type = null, string $server = 'default')
    {
        if (is_null($host)) {
            $host = config('api_server.' . $server . '.host');
        }
        parent::__construct($host);

        $this->type = $type;
        $this->server = $server;
        $this->config = ConfigParser::newInstance(config('api_server.' . $server));
    }

    /**
     * @param $name
     * @param $arguments
     * @return EndPoint|null
     */
    public static function __callStatic($name, $arguments)
    {
        /** @var ApiClient $static */
        $static = static::newInstance();

        return $static->$name($arguments);
    }

    /**
     * @param $name
     * @param $arguments
     * @return EndPoint|null
     */
    public function __call($name, $arguments)
    {
        $classPath = $this->getEndPointClass($name);
        if (is_null($classPath)) {
            return null;
        }

        return $this->makeEndPoint($classPath);
    }

    /**
     * @param string|null $host
     * @param string|null $type
     * @param string $server
     * @return static
     */
    public static function newInstance(string $host = null, string $type = null, string $server = 'default'): ApiClient
    {
        return new static($host, $type, $server);
    }

    /**
     * @param string $name
     * @return string|null
     */
    protected function getEndPointClass(string $name): ?string
    {
        $apis = $this->config('end_point');
        $classPath = null;
        foreach (array_keys($apis) as $key) {
            if ($name == $key) {
                $className = Str::camel($key);
                $classPath = config('api_server.' . $this->server . '.module_namespace') . "\\{$className}\\" . $className;
            }
        }

        return $classPath;
    }

    /**
     * @return string
     */
    public function endPoint(): string
    {
        return 'oauth';
    }

    /**
     * @param string $class
     * @return EndPoint|null
     */
    protected function makeEndPoint(string $class): ?EndPoint
    {
        if (class_exists($class)) {
            $object = new $class($this->host);
            if ($object instanceof EndPoint) {
                return $object->setConfig($this->config);
            }
        }

        return null;
    }

    /**
     * @return void
     */
    public function throw()
    {
        ErrorResponse::throw($this->response->json() ?? $this->response->body());
    }
}
