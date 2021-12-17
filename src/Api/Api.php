<?php


namespace Miniyus\RestfulApiClient\Api;


use ArrayAccess;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @mixn Client
 */
trait Api
{
    /**
     * @var ConfigParser
     */
    protected ConfigParser $config;

    /**
     * @param string|null $host
     * @param string $type
     * @param array|string $server
     */
    protected function initialize(string $host = null, string $type = 'storage', $server = 'default')
    {
        if (is_string($server)) {
            if (is_null($host)) {
                /** @var string|null $host */
                $host = config('api_server.' . $server . '.host', null);
            }

            $config = ConfigParser::newInstance(config('api_server.' . $server));
        } else if (is_array($server)) {
            if (is_null($host)) {
                $host = $server['host'];
            }
            $config = ConfigParser::newInstance($server);
            $server = null;
        } else {
            throw new \InvalidArgumentException('must be server parameter is string|array');
        }

        $this->host = $host;
        $this->type = $type;
        $this->server = $server;
        $this->config = $config;
    }

    /**
     * @param string|null $name
     * @return ConfigParser|array|ArrayAccess|mixed|null
     */
    public function config(string $name = null)
    {
        if (is_null($name)) {
            return $this->config;
        }
        return $this->config->get($name);
    }

    /**
     * @param string $host
     * @param string $name
     * @return string
     */
    public function makeUrl(string $host, string $name): string
    {
        $method = Str::lower($name);

        if (empty($this->endPoint())) {
            return $host . "/$method";
        }

        return $host . "/{$this->endPoint()}/$method";
    }

    /**
     * @param string|null $type
     * @return $this
     */
    public function setToken(string $token, string $type = null): self
    {
        switch ($type) {
            case 'storage':
                Storage::disk('local')->put(config("api_server.$this->server.token_storage.$type.name"), $token);
                break;
            case 'session':
                session([config("api_server.$this->server.token_storage.$type.name") => $token]);
                break;
            case 'model':
                $class = config("api_server.$this->server.token_storage.$type.name");
                $model = new $class;
                $model->setAttribute('access_token', $token);
                $model->save();
                break;
            case 'cookie':
                cookie(config("api_server.$this->server.token_storage.$type.name"), $token);
                break;
            default:
                if (!is_null($this->type)) {
                    $this->setToken($token, $this->type);
                }
                break;
        }

        return $this;
    }

    /**
     * @param string|null $type
     * @throws FileNotFoundException
     */
    public function getToken(string $type = null): ?string
    {
        $token = null;
        switch ($type) {
            case 'storage':
                if (Storage::disk('local')->exists(config("api_server.$this->server.token_storage.$type.name"))) {
                    $token = Storage::disk('local')->get(config("api_server.$this->server.token_storage.$type.name"));
                }
                break;
            case 'session':
                $token = session(config("api_server.$this->server.token_storage.$type.name"));
                break;
            case 'model':
                $class = config("api_server.$this->server.token_storage.$type.name");
                $model = new $class;
                $token = $model->orderByDesc('created_at')->first();
                break;
            case 'cookie':
                $token = Cookie::get(config("api_server.$this->server.token_storage.$type.name"));
                break;
            default:
                if (!is_null($this->type)) {
                    $token = $this->getToken($this->type);
                }
                break;
        }

        return $token;
    }

    /**
     * @return string
     */
    abstract public function endPoint(): string;

    /**
     * @param string|int|array $input
     * @return array
     */
    protected function parsePathParameter($input): array
    {
        $data = [];
        if (is_array($input)) {
            $data = $input;
        } else {
            $this->url .= "/$input";
        }

        return $data;
    }

    /**
     * @param array|string|null $input
     * @return array|string|null
     * @throws FileNotFoundException
     */
    public function get($input = null)
    {
        return $this->response(
            Http::withToken($this->getToken())->get($this->url, $input)
        );
    }

    /**
     * @param array $input
     * @return array|string|null
     * @throws FileNotFoundException
     */
    public function post(array $input = [])
    {
        return $this->response(
            Http::withToken($this->getToken())->post($this->url, $input)
        );
    }

    /**
     * @param array|string|int $input
     * @param array $data
     * @return array|string|null
     * @throws FileNotFoundException
     */
    public function put($input = [], array $data = [])
    {
        $data = $this->parsePathParameter($input);

        return $this->response(
            Http::withToken($this->getToken())->put($this->url, $data)
        );
    }

    /**
     * @param string|int|array $input
     * @return array|string|null
     * @throws FileNotFoundException
     */
    public function delete($input = [])
    {
        $data = $this->parsePathParameter($input);
        return $this->response(
            Http::withToken($this->getToken())->delete($this->url, $data)
        );
    }

    /**
     * show resource, id parameter is path parameter
     * @param string|int $id
     * @return null
     * @throws FileNotFoundException
     */
    public function show($id, array $params = null)
    {
        if (empty($id)) {
            throw new \InvalidArgumentException('show(): $id 파라미터는 필수 입니다.');
        }

        $this->url .= "/{$id}";
        return $this->get($params);
    }
}
