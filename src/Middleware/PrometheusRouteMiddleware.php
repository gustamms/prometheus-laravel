<?php

namespace Gustavomendes\PrometheusLaravel\Middleware;

use Closure;
use Gustavomendes\PrometheusLaravel\PrometheusCollector;
use Prometheus\Exception\MetricsRegistrationException;
use Symfony\Component\HttpFoundation\Request;
use Illuminate\Support\Facades\Route as RouteFacade;

class PrometheusRouteMiddleware
{
    private $prometheusCollector;

    /**
     * @throws MetricsRegistrationException
     */
    public function handle(Request $request, Closure $next)
    {
        $this->prometheusCollector = new PrometheusCollector();

        $response = $next($request);

        $this->prometheusCollector->getOrRegisterCounter(
            env('PROMETHEUS_NAMESPACE', 'app'),
            'request',
            'Request are made',
            ['uri', 'method', 'statusCode'],
            [$request->getRequestUri(), $request->getMethod() ,$response->getStatusCode()]
        );

        return $response;
    }
}
