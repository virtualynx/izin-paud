<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use VLynx\Sso\VAuthSsoClient;

class SsoLoginMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $server_url = config('app.sso.server_url');
        $client_id = config('app.sso.client_id');
        $client_secret = config('app.sso.client_secret');
        $server_url_local = config('app.sso.server_url_local');

        $ssoClient = new VAuthSsoClient($server_url, $client_id, $client_secret, $server_url_local);
        $token = $ssoClient->AuthCheck();

        return $next($request);
    }
}
