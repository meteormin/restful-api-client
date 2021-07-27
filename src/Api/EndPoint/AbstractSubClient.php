<?php


namespace Miniyus\RestfulApiClient\Api\EndPoint;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Miniyus\RestfulApiClient\Api\Api;
use Miniyus\RestfulApiClient\Api\Client;
use Miniyus\RestfulApiClient\Api\ConfigParser;
use Miniyus\RestfulApiClient\Api\Contracts\SubClient;
use Illuminate\Support\Facades\Http;

/**
 * Class AbstractSubClient
 * Basic HTTp Restful API methods
 * GET
 * POST
 * PUT
 * DELETE
 * @package App\Libraries\V1\EndPoint
 */
abstract class AbstractSubClient extends Client implements SubClient
{
    use Api;

    /**
     * @var string
     */
    public $url = '';

    /**
     * @var string
     */
    protected $namespace = '';

    /**
     * @var string
     */
    protected $endPoint = '';

    /**
     * AbstractSubClient constructor.
     * @param string|null $host
     */
    public function __construct(string $host = null)
    {
        parent::__construct($host);
    }

    /**
     * @return string
     */
    public function getNameSpace(): string
    {
        return $this->namespace;
    }

    /**
     * @param string $endPoint
     */
    public function setEndPoint(string $endPoint)
    {
        $this->endPoint = $endPoint;
    }

    /**
     * @return string
     */
    public function endPoint(): string
    {
        return $this->endPoint;
    }

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
    public function delete($input)
    {
        $data = [];
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
