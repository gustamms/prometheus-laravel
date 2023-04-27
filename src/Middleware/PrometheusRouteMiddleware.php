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
        $matchedRoute = $this->getMatchedRoute($request);

        $response = $next($request);

        $this->prometheusCollector->getOrRegisterCounter(
            env('PROMETHEUS_NAMESPACE', 'app'),
            'request_made',
            'Request are made',
            ['statusCode', 'uri'],
            [$response->getStatusCode(), $matchedRoute->uri()]
        );

        return $response;
    }

    public function getMatchedRoute(Request $request): \Illuminate\Routing\Route
    {
        $routeCollection = RouteFacade::getRoutes();
        return $routeCollection->match($request);
    }
}
