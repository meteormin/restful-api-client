<?php

namespace Miniyus\RestfulApiClient\Response;

interface ErrorCode
{
    /**
     * OAuth2.0 관련 에러 코드
     * 2 ~ 10
     */

    /**
     * 지원하지 않는 grant_type
     * @var int UNSUPPORTED_GRANT_TYPE
     */
    const UNSUPPORTED_GRANT_TYPE = 2;

    /**
     * 유효하지 않는 REQUEST
     * @var int INVALID_REQUEST
     */
    const INVALID_REQUEST = 3;

    /**
     * 유효하지 않는 CLIENT
     * @var int INVALID_CLIENT
     */
    const INVALID_CLIENT = 4;

    /**
     * 유효하지 않는 SCOPE
     * @var int INVALID_SCOPE
     */
    const INVALID_SCOPE = 5;

    /**
     * 유효하지 않는 CREDENTIALS
     * @var int INVALID_CREDENTIALS
     */
    const INVALID_CREDENTIALS = 6;

    /**
     * OAuth 서버 에러
     * @var int OAUTH_SERVER_ERROR
     */
    const OAUTH_SERVER_ERROR = 7;

    /**
     * 유효하지 않는 REFRESH_TOKEN
     * @var int INVALID_REFRESH_TOKEN
     */
    const INVALID_REFRESH_TOKEN = 8;

    /**
     * 접근 권한 없음
     * @var int ACCESS_DENIED
     */
    const ACCESS_DENIED = 9;

    /**
     * 존재하는 GRANT_TYPE이지만
     * 현재 요청한 클라이언트에겐 허용되지 않은 GRANT_TYPE인 경우?
     *
     * 실제로 해보니 보통 사용자 인증 실패 시 출력된다.
     * @var int INVALID_GRANT
     */
    const INVALID_GRANT = 10;

    /**
     * TongDoc API 관련 에러 코드
     * 20 ~ 99
     */

    /**
     * page not found 404
     * @var int NOT_FOUND
     */
    const NOT_FOUND = 20;

    /**
     * 해당 요청은 존재하지만 메서드가 틀린 경우
     * @var int ALLOW_NOT_METHOD
     */
    const ALLOW_NOT_METHOD = 21;

    /**
     * token이 필요한데, 토큰을 헤더에 싣지 않은 경우
     * @var int UNTOKEN
     */
    const UNTOKEN = 22;

    /**
     * 토큰이 만료된 경우이거나, 잘못된 토큰
     * @var int UNAUTHORIZED
     */
    const UNAUTHORIZED = 23;

    /**
     * 유효성 검사 실패
     * @var int VALIDATION_FAIL
     */
    const VALIDATION_FAIL = 24;

    /**
     * 접근 금지(권한 없음)
     * @var int FORBIDDEN
     */
    const FORBIDDEN = 25;

    /**
     * 데이터 충돌(중복 데이터)
     * @var int CONFLICT
     */
    const CONFLICT = 26;

    /**
     * 데이터가 존재하지 않는 경우
     * @var int RESOURCE_NOT_FOUND
     */
    const RESOURCE_NOT_FOUND = 27;

    /**
     * 쿼리 에러
     * @var int QUERY_ERROR
     */
    const QUERY_ERROR = 28;

    /**
     * 서버 점검
     * @var int SERVER_DOWN
     */
    const SERVER_DOWN = 29;

    // 추가 작성

    /**
     * 서버 에러
     * @var int SERVER_ERROR
     */
    const SERVER_ERROR = 99;

    public function getCode();

    public function setCode(int $code);
}
