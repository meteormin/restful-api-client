<?php


namespace Miniyus\RestfulApiClient\Api;

use Illuminate\Support\Arr;
use ArrayAccess;


/**
 * Class ConfigParser
 * @package App\Libraries\V1
 * @method array|ArrayAccess oauth(string $argument = null)
 */
class ConfigParser
{
    /**
     * @var array|null
     */
    protected ?array $config;

    /**
     * ConfigParser constructor.
     * @param array|null $config
     */
    public function __construct(?array $config)
    {
        $this->config = $config;
    }

    /**
     * @param array|null $config
     * @return static
     */
    public static function newInstance(?array $config): ConfigParser
    {
        return new static($config);
    }

    /**
     * call end point
     * $this->api() == config('api_server.{server}.end_point.api')
     */
    public function __call($name, $argument)
    {
        $arg = $argument[0] ?? null;
        if (is_null($arg)) {
            return $this->get("end_point.{$name}");
        }

        return $this->get("end_point.{$name}.{$arg}");
    }

    /**
     * @param string|null $key
     * @param mixed|null $default
     * @return array|ArrayAccess|mixed
     */
    public function get(string $key = null, $default = null)
    {
        if (is_null($key)) {
            return $this->config;
        }

        return Arr::get($this->config, $key, $default);
    }

    /**
     * @return array|ArrayAccess|mixed|null
     */
    public function all()
    {
        return $this->get("end_point");
    }

}
