<?php

namespace Gustamms\PrometheusLaravel\Providers;

use Gustamms\PrometheusLaravel\Controllers\MetricController;
use Illuminate\Routing\Route;
use Illuminate\Support\ServiceProvider;
use Prometheus\CollectorRegistry;

class PrometheusServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/prometheus.php' => $this->configPath('prometheus.php'),
        ]);

        if (env('PROMETHEUS_STORAGE_ADAPTER', 'memory') == 'redis') {
            $this->loadRedis();
        }

        $this->app->alias(CollectorRegistry::class, 'prometheus');

        $this->loadRoutes();
    }

    private function loadRedis(): void
    {
        $prometheus = config("prometheus.storage_adapters.redis");
        $redisHost = $prometheus['host'];
        $redisHost = explode(',', $redisHost);
        $redisHosts = array_map('trim', $redisHost);

        if (str_contains($redisHosts[0], ':')) {
            $redisHosts = explode(':', $redisHost[0]);
        }

        \Prometheus\Storage\Redis::setDefaultOptions(
            [
                'host' => $redisHosts[0],
                'port' => (int) $prometheus['port'],
                'password' => null,
                'timeout' => 0.1, // in seconds
                'read_timeout' => '10', // in seconds
                'persistent_connections' => false
            ]
        );
    }

    private function loadRoutes()
    {
        $router = $this->app['router'];
        $metricRoute = config('prometheus.metrics_route_path');

        /** @var Route $route */
        $isLumen = mb_strpos($this->app->version(), 'Lumen') !== false;
        if ($isLumen) {
            $router->get(
                '/' . $metricRoute,
                [
                    'as' => 'metrics',
                    'uses' => MetricController::class . '@getMetrics',
                ]
            );
        } else {
            $router->get(
                '/' . $metricRoute,
                MetricController::class . '@getMetrics'
            )->name('metrics');
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/prometheus.php', 'prometheus');

    }

    private function configPath($path): string
    {
        return $this->app->basePath() . ($path ? DIRECTORY_SEPARATOR . $path : '');
    }
}
