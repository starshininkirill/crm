<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IpWhitelist
{
    // Массив разрешенных IP и подсетей (можно использовать * для замены любого октета)
    protected $allowedIps = [
        '127.0.0.1',
        'localhost',
        '5.23.55.43',
        '192.168.1.*',
        '192.168.0.*',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $clientIp = $request->ip();

        if (!$this->isIpAllowed($clientIp)) {
            return response('Unauthorized', 403);
        }

        return $next($request);
    }

    /**
     * Проверяет, разрешен ли IP адрес
     */
    protected function isIpAllowed(string $ip): bool
    {
        foreach ($this->allowedIps as $allowedIp) {
            if ($this->matchIp($ip, $allowedIp)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Сравнивает IP клиента с шаблоном из whitelist
     */
    protected function matchIp(string $ip, string $pattern): bool
    {
        // Локальный хост - специальный случай
        if ($pattern === 'localhost') {
            return in_array($ip, ['127.0.0.1', '::1']);
        }

        // Точное совпадение
        if ($ip === $pattern) {
            return true;
        }

        // Проверка шаблонов с wildcard (*)
        $patternParts = explode('.', $pattern);
        $ipParts = explode('.', $ip);

        // Проверяем что оба адреса IPv4 и имеют 4 части
        if (count($patternParts) !== 4 || count($ipParts) !== 4) {
            return false;
        }

        // Пооктетное сравнение с учетом wildcard
        for ($i = 0; $i < 4; $i++) {
            if ($patternParts[$i] !== '*' && $patternParts[$i] !== $ipParts[$i]) {
                return false;
            }
        }

        return true;
    }
}
