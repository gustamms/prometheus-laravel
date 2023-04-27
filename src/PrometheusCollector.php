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

    /**
     * @throws MetricsRegistrationException
     */
    public function getOrRegisterHistogram(
        string $namespace,
        string $name,
        string $help,
        float $number,
        array $labels = [],
        array $labelsValues = [],
        array $buckets = null
    )
    {
        $this->collector
            ->getOrRegisterHistogram($namespace, $name, $help, $labels, $buckets)
            ->observe($number, $labelsValues);
    }

    /**
     * @throws MetricsRegistrationException
     */
    public function getOrRegisterSummary(
        string $namespace,
        string $name,
        string $help,
        int $number,
        array $labels = [],
        array $labelsValues = [],
        int $maxAgeSeconds = 600,
        array $quantiles = null
    )
    {
        $this->collector
            ->getOrRegisterSummary($namespace, $name, $help, $labels, $maxAgeSeconds, $quantiles)
            ->observe($number, $labelsValues);
    }
}
