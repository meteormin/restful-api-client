<?php

namespace Miniyus\RestfulApiClient\Response;

use Illuminate\Http\Exceptions\HttpResponseException;

class ErrorResponse
{
    /**
     * @var
     */
    private static $error;

    /**
     *
     */
    public static function init()
    {
        self::$error = __('error');
    }

    /**
     * @param $message
     * @param int $code
     * @param string $type html, json
     */
    public static function throw($message, int $code = ErrorCode::SERVER_ERROR, string $type = 'html')
    {
        self::init();

        if ($type == 'html') {
            abort(self::$error[$code]['http'], $message);
        } else {
            throw new HttpResponseException(response()->json($message, self::$error[$code]['http']));
        }
    }
}
