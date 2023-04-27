<?php

namespace Gustavomendes\PrometheusLaravel;

use Prometheus\Exception\MetricsRegistrationException;

class PrometheusCollector
{
    private $collector;
    public function __construct()
    {
        $this->collector = \Prometheus\CollectorRegistry::getDefault();
    }

    /**
     * @throws MetricsRegistrationException
     */
    public function getOrRegisterCounter(
        string $namespace,
        string $name,
        string $help,
        array  $labels = [],
        array  $labelsValues = []
    )
    {
        $this->collector
            ->getOrRegisterCounter($namespace, $name, $help, $labels)
            ->inc($labelsValues);
    }
}
