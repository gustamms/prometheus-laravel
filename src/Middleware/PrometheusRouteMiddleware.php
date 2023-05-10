<?php

namespace Gustamms\PrometheusLaravel\Middleware;

use Closure;
use Gustamms\PrometheusLaravel\Metrics\Counter;
use Gustamms\PrometheusLaravel\Metrics\Histogram;
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

        $labels = [
            'uri' => $path,
            'method' => $request->getMethod(),
            'statusCode' => (string)$response->getStatusCode()
        ];

        Counter::add(
            1,
            'request',
            $labels,
            'Request are made'
        );

        Histogram::record(
            $duration,
            'request_time',
            $labels,
            'Time request made'
        );

        return $response;
    }
}
