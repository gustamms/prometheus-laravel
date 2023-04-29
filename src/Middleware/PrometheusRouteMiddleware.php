<?php

namespace Gustamms\PrometheusLaravel\Middleware;

use Closure;
use Gustamms\PrometheusLaravel\PrometheusCollector;
use Prometheus\Exception\MetricsRegistrationException;
use Symfony\Component\HttpFoundation\Request;

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

        if (app() instanceof \Illuminate\Foundation\Application && $request->route()) {
            $params = $request->route()->parameters();
        } elseif (isset($request->route()[2])) {
            $params = $request->route()[2];
        } else {
            $params = [];
        }
        $path = $request->path();

        if (isset($params) && count($params)) {
            foreach ($params as $key => $value) {
                $path = str_replace($value, "{" . $key . "}", $path);
            }
        }

        $this->prometheusCollector->getOrRegisterCounter(
            'request',
            'Request are made',
            ['uri', 'method', 'statusCode'],
            [$path, $request->getMethod() ,$response->getStatusCode()]
        );

        $this->prometheusCollector->getOrRegisterHistogram(
            'request_time',
            'Time request made',
            $duration,
            ['uri', 'method', 'statusCode'],
            [$path, $request->getMethod() ,$response->getStatusCode()]
        );

        return $response;
    }
}
