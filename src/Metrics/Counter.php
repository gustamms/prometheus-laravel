<?php

namespace Gustamms\PrometheusLaravel\Metrics;

use Gustamms\PrometheusLaravel\PrometheusCollector;
use Prometheus\CollectorRegistry;
use Prometheus\Storage\APCng;

class Counter
{
    public static function add(
        $amount,
        string  $name,
        ?array  $labels = [],
        ?string $description = '',
        ?string $namespace = '',
    )
    {
        if ($amount < 0) {
            throw new \InvalidArgumentException('Amount can not be negative');
        }

        $labelsKeys = array_keys($labels);
        $labelsValues = array_values($labels);

        $registry = (new PrometheusCollector())->getCollector();
        $registry->getOrRegisterCounter(
                $namespace,
                env('PROMETHEUS_NAMESPACE') . $name,
                $description,
                $labelsKeys
            )
            ->incBy($amount, $labelsValues);
    }
}
