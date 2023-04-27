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