<?php

namespace Gustavomendes\PrometheusLaravel\Providers;

use Gustavomendes\PrometheusLaravel\Controllers\MetricController;
use Illuminate\Routing\Route;
use Illuminate\Support\ServiceProvider;

class PrometheusServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/prometheus.php' => $this->configPath('prometheus.php'),
        ]);
        // $this->loadRoutesFrom(__DIR__ . '/../../routes/routes.php');
        if (env('PROMETHEUS_STORAGE_ADAPTER', 'memory') == 'redis') {
            $this->loadRedis();
        }
        $this->loadRoutes();
    }

    private function loadRedis(): void
    {
        \Prometheus\Storage\Redis::setDefaultOptions(
            [
                'host' => env('PROMETHEUS_REDIS_HOST', '127.0.0.1'),
                'port' => 6379,
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

        /** @var Route $route */
        $isLumen = mb_strpos($this->app->version(), 'Lumen') !== false;
        if ($isLumen) {
            $router->get(
                '/metrics',
                [
                    'as' => 'metrics',
                    'uses' => MetricController::class . '@getMetrics',
                ]
            );
        } else {
            $router->get(
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
