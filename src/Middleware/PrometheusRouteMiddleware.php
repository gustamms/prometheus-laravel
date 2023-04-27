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

    private $except = [
        'health', '/', '', '/metrics', 'api/metrics', 'metrics', 'favicon.ico', 'documentation', 'docs'
    ];

    /**
     * @throws MetricsRegistrationException
     */
    public function handle(Request $request, Closure $next)
    {
        if (in_array($request->path(), $this->except)) {
            return $next($request);
        }
        $start = microtime(true);
        $this->prometheusCollector = new PrometheusCollector();

        $response = $next($request);
        $duration = microtime(true) - $start;

        $this->prometheusCollector->getOrRegisterCounter(
            'request',
            'Request are made',
            ['uri', 'method', 'statusCode'],
            [$request->getRequestUri(), $request->getMethod() ,$response->getStatusCode()]
        );

        $this->prometheusCollector->getOrRegisterHistogram(
            'request_time',
            'Time request made',
            $duration,
            ['uri', 'method', 'statusCode'],
            [$request->getRequestUri(), $request->getMethod() ,$response->getStatusCode()]
        );

        return $response;
    }
}
