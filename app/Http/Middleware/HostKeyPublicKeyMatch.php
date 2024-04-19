<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HostKeyPublicKeyMatch
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $event = $request->route()->parameter('event');
        $publicKey = $request->route()->parameter('public_key');

        abort_if($event->public_key !== $publicKey, 404);

        return $next($request);
    }
}
