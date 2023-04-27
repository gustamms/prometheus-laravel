# Laravel/Lumen Prometheus Metrics

## To use

Add this following repository in `composer.json` file
```bash
    {
        "type": "vcs",
        "url": "https://github.com/gustamms/prometheus-laravel.git"
    }
```

After run this command
```bash
    composer require gustavomendes/prometheus-laravel
```

Copy the `config/prometheus.php` to your laravel/lumen implementation

https://github.com/gustamms/prometheus-laravel/blob/main/src/config/prometheus.php

In `bootstrap/app.php` add this configure
```bash
$app->configure('prometheus');
```

In `bootstrap/app.php` add this register provider
```bash
$app->register(\Gustavomendes\PrometheusLaravel\Providers\PrometheusServiceProvider::class);
```

In `bootstrap/app.php` add this middleware
```bash
$app->middleware([
    ...
    \Gustavomendes\PrometheusLaravel\Middleware\PrometheusRouteMiddleware::class
]);

```

In `.env` add and change the values of this lines 
```bash
PROMETHEUS_STORAGE_ADAPTER=redis
PROMETHEUS_REDIS_HOST=redis.zendesk-support.dev
PROMETHEUS_REDIS_PORT=6379
PROMETHEUS_REDIS_TIMEOUT=0.1
PROMETHEUS_REDIS_READ_TIMEOUT=10
PROMETHEUS_REDIS_PERSISTENT_CONNECTIONS=0
PROMETHEUS_REDIS_PREFIX=PROMETHEUS_
PROMETHEUS_NAMESPACE=application_name
```