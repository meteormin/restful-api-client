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
 * Basic HTTP Restful API methods
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
    public string $url = '';

    /**
     * @var string
     */
    protected string $namespace = '';

    /**
     * @var string
     */
    protected string $endPoint = '';

    /**
     * @var string
     */
    protected string $type;

    /**
     * @var string|null
     */
    protected ?string $server;

    /**
     * AbstractEndPoint constructor.
     * @param string|null $host
     */
    public function __construct(string $host = null, string $type = 'storage', string $server = 'default')
    {
        parent::__construct($host);
        $this->initialize($host, $type, $server);
    }

    /**
     * @return string
     */
    public function endPoint(): string
    {
        return $this->endPoint;
    }

    /**
     * @param string $endPoint
     */
    public function setEndPoint(string $endPoint): void
    {
        $this->endPoint = $endPoint;
    }

    /**
     * @return string
     */
    public function getNameSpace(): string
    {
        return $this->namespace;
    }
}
