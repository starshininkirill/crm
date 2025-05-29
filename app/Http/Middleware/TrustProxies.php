<?php

namespace App\Http\Middleware;

use \Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * Доверенные прокси этого приложения.
     *
     * @var string|array
     */
    protected $proxies = [
        '192.168.0.1/21',
    ];

    /**
     * Заголовки, используемые для обнаружения прокси.
     *
     * @var int
     */
    protected $headers = Request::HEADER_X_FORWARDED_FOR | Request::HEADER_X_FORWARDED_HOST | Request::HEADER_X_FORWARDED_PORT | Request::HEADER_X_FORWARDED_PROTO;
}
