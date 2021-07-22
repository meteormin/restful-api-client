<?php


namespace Miniyus\RestfulApiClient\Api;


use ArrayAccess;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait Api
{
    /**
     * @var ConfigParser
     */
    protected ConfigParser $config;

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

        return $host . "/{$this->endPoint()}/{$method}";
    }

    /**
     * @param string|null $type
     * @return $this
     */
    public function setToken(string $token, string $type = null): self
    {
        switch ($type) {
            case 'storage':
                Storage::disk('local')->put(config('api_server.token_storage.storage'), $token);
                break;
            case 'session':
                session()->put(config('api_server.token_storage.session'), $token);
                break;
            case 'model':
                $class = config('api_server.token_storage.model');
                $model = new $class;
                $model->setAttribute('access_token', $token);
                $model->save();
                break;
            case 'cookie':
                cookie(config('api_server.token_storage.session'), $token);
                break;
            default:
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
                $token = Storage::disk('local')->get(config('api_server.token_storage.storage'));
                break;
            case 'session':
                session()->get(config('api_server.token_storage.session'));
                break;
            case 'model':
                $class = config('api_server.token_storage.model');
                $model = new $class;
                $token = $model->orderByDesc('created_at')->first();
                break;
            case 'cookie':
                $token = Cookie::get(config('api_server.token_storage.session'));
                break;
            default:
                break;
        }

        return $token;
    }


    /**
     * @return string
     */
    abstract public function endPoint(): string;

}
