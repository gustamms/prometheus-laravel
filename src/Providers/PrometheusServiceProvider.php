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
        $this->loadRoutes();
    }

    private function loadRoutes()
    {
        $router = $this->app['router'];

        /** @var Route $route */
        $isLumen = mb_strpos($this->app->version(), 'Lumen') !== false;
        if ($isLumen) {
            $router->get(
                [
                    'as' => 'metrics',
                    'uses' => MetricController::class . '@tested',
                ]
            );
        } else {
            $router->get(
                MetricController::class . '@tested'
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