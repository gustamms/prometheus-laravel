<?php

namespace Gustavomendes\PrometheusLaravel\Middleware;

use Closure;
use Symfony\Component\HttpFoundation\Request;

class PrometheusRouteMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        \Prometheus\CollectorRegistry::getDefault()
            ->getOrRegisterCounter('', 'teste_com_lib', 'just a quick measurement')
            ->inc();
        $response = $next($request);
        return $response;
    }
}