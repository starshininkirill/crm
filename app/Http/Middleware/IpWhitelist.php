<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IpWhitelist
{
    protected $allowedIps = ['127.0.0.1', 'localhost', '5.23.55.43', '192.168.1.146'];
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $clientIp = $request->ip();

        if (!in_array($clientIp, $this->allowedIps)) {
            return response('Unauthorized', 403);
        }

        return $next($request);
    }
}
