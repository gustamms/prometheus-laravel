<?php

namespace Gustavomendes\PrometheusLaravel;

use Prometheus\Exception\MetricsRegistrationException;

class PrometheusCollector
{
    private $collector;
    private $namespace;

    public function __construct()
    {
        $this->collector = \Prometheus\CollectorRegistry::getDefault();
        $this->namespace = env('PROMETHEUS_NAMESPACE', 'app');
    }

    /**
     * @throws MetricsRegistrationException
     */
    public function getOrRegisterCounter(
        string $name,
        string $help,
        array  $labels = [],
        array  $labelsValues = []
    )
    {
        $this->collector
            ->getOrRegisterCounter($this->namespace, $name, $help, $labels)
            ->inc($labelsValues);
    }

    /**
     * @throws MetricsRegistrationException
     */
    public function getOrRegisterHistogram(
        string $name,
        string $help,
        float $number,
        array $labels = [],
        array $labelsValues = [],
        array $buckets = null
    )
    {
        $this->collector
            ->getOrRegisterHistogram($this->namespace, $name, $help, $labels, $buckets)
            ->observe($number, $labelsValues);
    }

    /**
     * @throws MetricsRegistrationException
     */
    public function getOrRegisterSummary(
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
            ->getOrRegisterSummary($this->namespace, $name, $help, $labels, $maxAgeSeconds, $quantiles)
            ->observe($number, $labelsValues);
    }
}
