# RestfulApiClient For Laravel

  <img alt="Version" src="https://img.shields.io/badge/version-1.1.10-blue.svg?cacheSeconds=2592000" />
  <a href="https://php.net" target="_blank">
    <img src="https://img.shields.io/badge/php-%5E7.4.0-blue" />
  </a>
  <a href="https://github.com/miniyus/tongdocAPI#readme" target="_blank">
    <img alt="Documentation" src="https://img.shields.io/badge/documentation-yes-brightgreen.svg" />
  </a>
  <a href="https://github.com/miniyus/tongdocAPI/graphs/commit-activity" target="_blank">
    <img alt="Maintenance" src="https://img.shields.io/badge/Maintained%3F-yes-green.svg" />
  </a>
  <a href="https://github.com/miniyus/tongdocAPI/blob/master/LICENSE" target="_blank">
    <img alt="License: MIT" src="https://img.shields.io/badge/license-MIT-yellow" />
  </a>

Laravel용 restful API개발 도구입니다. restful API의 url구조를 클래스로 구현하기 쉽게 Abstract 클래스들을 제공합니다.

## Preview

```php
<?php
use \Miniyus\RestfulApiClient\Api\ApiClient;

// GET https://api.exmaple.com/v1/user
$response = ApiClient::v1()->user()->get();

// POST https://api.example.com/v1/user
$request = ['something'=>''];
$response = ApiClient::v1()->user()->post($request);

// PUT https://api.example.com/v1/user
$request = ['something'=>''];
$response = ApiClient::v1()->user()->put($request);

// if you have path parameter
// PUT https://api.example.com/v1/user/1 
$id = 1;
$request = ['something'=>''];
$response = ApiClient::v1()->user()->put($id, $request);

// DELETE https://api.example.com/v1/user
$response = ApiClient::v1()->user()->delete();

// if you have path parameter
// DELETE https://api.example.com/v1/user
$id = 1;
$response = ApiClient::v1()->user()->delete($id);
```

## Install

1. composer install

```shell
composer require miniyus/restful-api-client
```

2. Laravel artisan command

```shell
php artisan vendor:publish --provider="Miniyus\RestfulApiClient\ApiClientServiceProvider"
```

## Usage

1. setting config file

```php
<?php

return [
    // server
    'default' => [
        'host' => env('API_SERVER', 'api 서버 url'),
        'module_namespace' => '개발하고자 하는 클라이언트 모듈의 경로',
        // 지원하는 토큰 저장 매체
        'token_storage' => [
            'storage' => ['name' => 'access_token'], // 파일 시스템
            'model' => ['name' => null], // 모델은 아직 지원하지 않습니다.
            'session' => ['name' => 'access_token'], // session에 저장
            'cookie' => ['name' => 'access_token'] // 쿠키에 저장
        ],
        'end_point' => [
            /** end point ex) https://api.example.com/v1 => v1 */
            'v1'=>['v1 end point에서 사용할 기타 설정 정의'],
        ]
    ],
    // 다른 url의 api가 필요한 경우 추가할 수 있습니다.
    'api'=>[
        'host'=>'api.example.com',
        'module_namespace' => '개발하고자 하는 클라이언트 모듈의 경로',
        // 지원하는 토큰 저장 매체
        'token_storage' => [
            'storage' => ['name' => 'access_token'], // 파일 시스템
            'model' => ['name' => null],
            'session' => ['name' => 'access_token'], // session에 저장
            'cookie' => ['name' => 'access_token'] // 쿠키에 저장
        ],
        'end_point' => [
            /** end point ex) https://api.example.com/v1 => v1 */
            'v1',
        ]
    ]
];

```

> 클래스에서 config 접근
>> config() 메서드는 Api Trait에 구현되어 있습니다.

```php
use \Miniyus\RestfulApiClient\Api\ApiClient;

$client = ApiClient::v1();
$client->config() // returned ConfigParser
$client->config()->api() // == config('api_server.{server}.end_point.api')
$client->config('end_point') // == config('api_server.{server}.end_point')
$client->config('host') // == config('api_server.{server}.host')
```

2. extends ApiClient(루트 클래스)

```php
<?php

use Miniyus\RestfulApiClient\Api\ApiClient;

class MainClient extends ApiClient{

    /**
     * config/api_server.php에서 default만 사용할 경우 별도의 클래스 구현이 필요 없습니다.
     * 다른 url의 api server의 요청을 구현하고자 한다면 현재 예제와 같이 생성자의 기본 값을 오버라이딩 하면 됩니다.
     *
     * @param string|null $host api server url / 기본 값: null
     * @param string $type token_storage 타입 / 기본 값: storage
     * @param string $server config 파일에 등록한 다른 url의 api 적용할 수 있습니다. / 기본값: default
     */
    public function __construct(string $host = null,string $type = 'storage',string $server = 'api') 
    {
        parent::__construct($host,$type,$server);
    }
}

```

3. extends AbstractEndPoint(end point 클래스)

```php
<?php

use Miniyus\RestfulApiClient\Api\EndPoint\AbstractEndPoint;

/**
 * Class V1
 * 클래스명은 ApiClient::{className}() 형태로 상위 객체에서 호출 시 사용되며,
 * endPoint() 메서드의 경우는 실제 url에 포함되어 요청을 보냅니다.
 * ex) 
 * className = Api
 * endPoint = v1
 * 
 * 호출: ApiClient::api()
 * 생성되는 api 요청 url: {host}/v1
 */
class V1 extends AbstractEndPoint
{
    public function endPoint() : string
    {
        return 'v1';
    }
}

```

4. extends AbstractSubClient(sub client 클래스)

```php
<?php

use Miniyus\RestfulApiClient\Api\EndPoint\AbstractSubClient;

class User extends AbstractSubClient
{

    /**
     * 예시 요청: GET v1/user
     * 이러한 간단한 요청의 경우는 기본 제공하는 get() 메서드를 사용하면 됩니다.
     * 예시: MainClient::v1()->user()->get()
     */
    public function getUser()
    {
        return $this->get();
    }
    
    /**
     * 만약 추가적인 경로가 필요할 경우 기존 메서드를 활용하여 정의합니다. 
     * 예시 요청: GET v1/user/ids 
     */
    public function getUserIds()
    {
       $this->url .= '/ids';
       return $this->get();
    }
}

// sub client는 기본적으로 get, post, put, delete, show 등의 메서드를 지원합니다.
// 기본 제공 메서드들은 자동으로 현재 클래스(resource 역할)를 요청에 추가 합니다.
// User 클래스면, v1/user 형식의 경로를 자동으로 만들어 줍니다.
// 사용법 또한 Laravel의 Http파사드를 사용하고 있기 때문에 Http 파사드와 유사합니다.
// 기존 Http 파사드에서 url 관련 파라미터만 빠져 있습니다.
// 자세한 내용은 아래의 AbstractSubClient 추상 클래스를 참고

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
     * @var string|null
     */
    protected ?string $type;

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
        if (is_null($host)) {
            $host = config("api_server.$server");
        }

        parent::__construct($host);

        $this->type = $type;
        $this->server = $server;
        $this->config = ConfigParser::newInstance(config('api_server.' . $server));
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

```

4. 기타 부가 기능 설명 및 구현 가이드

```php
<?php

// GET api.example.com/v1/user

// ApiClient 혹은 상속 받은 클래스
// api.example.com/v1
$endPointV1 = ApiClient::v1();

// api.example.com/v1/user
$resourceUser = $endPointV1->user();

// GET api.example.com/v1/user
$resourceUser->get();

// show 메서드 활용
$id = 1;
// GET api.example.com/v1/user/1
$resourceUser->show($id);

```

> SubClient 구조 및 기능 설명

```php
    /**
     * restfulAPI url 구조에 따라 구현해둔 클래스들의 폴더 구조 형태로 작성되는 최종 url
     * @var string
     */
    public string $url = '';

    /**
     * 일종의 카테고리 역할
     *  v1/{category}/{resource} 과 같은 url구조일 경우 namespace 속성이 category가 된다.
     * 
     * @var string
     */
    protected string $namespace = '';

    /**
     * endpoint 상위 클래스들로부터 받아온다.
     * @var string
     */
    protected string $endPoint = '';

    /**
     *  endpoint 상위 클래스들로부터 받아온다.
     * @var string|null
     */
    protected ?string $type;

    /**
     *  endpoint 상위 클래스들로부터 받아온다. 
     * @var string|null
     */
    protected ?string $server;
```

- get()
    - 일반적인 get요청, 파라미터는 배열로 요청한다.
- post()
    - 일반적인 post요청,파라미터는 배열로 요청한다.
- put()
    - 일반적인 put 요청, 파라미터는 배열로 요청한다.
    - put 요청의 경우 url경로에 id 값(path parameter)을 넣어 보낼 수 있기 때문에, 첫 번째 파라미터가 배열이 아니면, 첫 번째 파라미터는 요청 맨 뒤에 추가된다.
        - put(1,['a'=>1,'b'=>2]) > put v1/user/1, request-body: {"a":1,"b":2}
- delete()
    - 일반적인 delete 요청, 파라미터는 배열로 요청한다.
    - delete 요청도 마찬가지로 url 경로에 id 값이 포함될 수 있어, put과 같이 처리된다.
- show()
    - show($id)는 조회용으로, get v1/user/1 과 같은 기능을 수행한다.
