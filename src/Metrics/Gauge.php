<?php

namespace PGustamms\PrometheusLaravel\Metrics;

use Gustamms\PrometheusLaravel\PrometheusCollector;
use Prometheus\CollectorRegistry;
use Prometheus\Storage\APCng;

class Gauge
{
    public static function add(
        $amount,
        string $name,
        ?array $labels = [],
        ?string $description = '',
        ?string $namespace = '',
    )
    {
        $labelsKeys = array_keys($labels);
        $labelsValues = array_values($labels);

        $registry = (new PrometheusCollector())->getCollector();
        $registry->getOrRegisterGauge($namespace, $name, $description, $labelsKeys)
            ->incBy($amount, $labelsValues);
    }
}
