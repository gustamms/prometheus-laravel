<?php

namespace Gustavomendes\PrometheusLaravel\Middleware;

use Closure;
use Gustavomendes\PrometheusLaravel\PrometheusCollector;
use Symfony\Component\HttpFoundation\Request;

class PrometheusRouteMiddleware
{
    private $prometheusCollector;
    public function handle(Request $request, Closure $next)
    {
        $this->prometheusCollector = new PrometheusCollector();

        $this->prometheusCollector->getOrRegisterCounter(
            'ms_zendesk_support',
            'test_com_collector',
            'teste qualquer'
        );

        $response = $next($request);
        return $response;
    }
}