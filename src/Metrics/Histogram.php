<?php

namespace Gustamms\PrometheusLaravel\Metrics;

use Gustamms\PrometheusLaravel\PrometheusCollector;
use Prometheus\CollectorRegistry;
use Prometheus\Storage\APCng;

class Histogram
{
    public static function record(
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
        $registry->getOrRegisterHistogram(
                $namespace,
                $name,
                $description,
                $labelsKeys
            )
            ->observe($amount, $labelsValues);
    }
}
