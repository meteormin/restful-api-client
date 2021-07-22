<?php


namespace Miniyus\RestfulApiClient\Api;


use Miniyus\RestfulApiClient\Api\Contracts\EndPoint;
use Miniyus\RestfulApiClient\Response\ErrorResponse;
use Illuminate\Http\Client\Response;

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
     * @var string
     */
    protected string $type;

    /**
     * ApiClient constructor.
     */
    public function __construct(string $host = null, string $type = null)
    {
        if (is_null($host)) {
            $host = config('api_server.host');
        }
        parent::__construct($host);
        $this->type = config('api_server.token_storage.' . $type);
        $this->config = ConfigParser::newInstance(config('api_server'));
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
        $apis = $static->config('end_point');
        foreach (array_keys($apis) as $key) {
            if ($name == $key) {
                $className = ucfirst($key);
                $classPath = config('api_server.module_namespace') . "\\{$className}\\" . $className;
                return $static->makeEndPoint($classPath);
            }
        }

        return null;
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
    public function makeEndPoint(string $class): ?EndPoint
    {
        if (class_exists($class)) {
            $object = new $class;
            if ($object instanceof EndPoint) {
                return $object;
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
