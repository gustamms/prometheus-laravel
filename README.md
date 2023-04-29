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
    composer require gustamms/prometheus-laravel
```

Copy the `config/prometheus.php` to your laravel/lumen implementation

https://github.com/gustamms/prometheus-laravel/blob/main/src/config/prometheus.php

In `bootstrap/app.php` add this configure
```bash
$app->configure('prometheus');
```

In `bootstrap/app.php` add this register provider
```bash
$app->register(\Gustamms\PrometheusLaravel\Providers\PrometheusServiceProvider::class);
```

In `bootstrap/app.php` add this middleware
```bash
$app->middleware([
    ...
    \Gustamms\PrometheusLaravel\Middleware\PrometheusRouteMiddleware::class
]);

```

In `.env` add and change the values of this lines 
```bash
PROMETHEUS_STORAGE_ADAPTER=redis
PROMETHEUS_REDIS_HOST=localhost
PROMETHEUS_REDIS_PORT=6379
PROMETHEUS_REDIS_TIMEOUT=0.1
PROMETHEUS_REDIS_READ_TIMEOUT=10
PROMETHEUS_REDIS_PERSISTENT_CONNECTIONS=0
PROMETHEUS_REDIS_PREFIX=PROMETHEUS_
PROMETHEUS_NAMESPACE=application_name
```

## How use for personal metrics?

```php
use Gustamms\PrometheusLaravel\PrometheusCollector;

class DoSomething 
{
    private $collector;
    
    public function __construct() {
        $this->collector = new PrometheusCollector();
    }
    
    public function do(){
        $this->collector->getOrRegisterCounter(
            'do_method_use',
            'Pass in Do method'
        );
        
        $this->collector->getOrRegisterHistogram(
            'histogram_sample',
            'Histogram are made',
            1.2,
            ['label1'],
            ['labelvalue1']
        );
    }
}
```