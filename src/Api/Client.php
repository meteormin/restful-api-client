<?php

namespace Miniyus\RestfulApiClient\Api;

use Illuminate\Http\Client\Response;

/**
 * Laravel Http Client Control helper
 */
abstract class Client
{
    /**
     * @var string|null
     */
    protected $host;

    /**
     * @var Response|null
     */
    protected Response $response;

    /**
     * @var array|string|null
     */
    protected $error;

    /**
     * 생성자
     * 생성하면서 host를 설정할 수 있다.
     *
     * @param string|null $host [$host description]
     */
    public function __construct(string $host = null)
    {
        $this->host = $host;
        $this->response = null;
        $this->error = null;
    }

    /**
     * @param string|null $host
     * @return static
     */
    public static function newInstance(string $host = null): self
    {
        return new static($host);
    }

    /**
     * 요청 결과에 맞게 응답을 준다.
     * error면 error속성에
     * 정상결과면 response속성에 결과를 대입
     * @param Response $response
     *
     * @return array|mixed|string|null
     */
    protected function response(Response $response)
    {
        $this->response = $response;

        if ($response->successful()) {
            return $response->json() ?? $response->body();
        } else {
            $this->error = $response->json() ?? $this->response->body();
            return $this->error;
        }
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return is_null($this->getError());
    }

    public function getResponse(): Response
    {
        return $this->response;
    }

    /**
     * @return array|string|null
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Get the value of host
     * @return string
     */
    public function getHost(): ?string
    {
        return $this->host;
    }

    /**
     * Set the value of host
     * @param string $host
     * @return  $this
     */
    public function setHost(string $host): Client
    {
        $this->host = $host;

        return $this;
    }
}
