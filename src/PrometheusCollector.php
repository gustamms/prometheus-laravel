<?php

namespace Gustamms\PrometheusLaravel;

use Prometheus\CollectorRegistry;
use Prometheus\Exception\MetricsRegistrationException;
use Prometheus\RenderTextFormat;
use Prometheus\Storage\InMemory;
use Exception;

class PrometheusCollector
{
    private $collector;
    private $namespace;

    public function __construct()
    {
        $storageAdapter = env('PROMETHEUS_STORAGE_ADAPTER');
        if (!$storageAdapter)  {
            throw new Exception('Variável PROMETHEUS_STORAGE_ADAPTER não informada');
        }

        switch ($storageAdapter) {
            case 'redis':
                $this->collector = \Prometheus\CollectorRegistry::getDefault();
                break;
            case 'memory':
                $this->collector = new CollectorRegistry(new InMemory());
                break;
        }

        $this->namespace = config("prometheus.namespace");
    }

    public function renderMetrics()
    {
        $registry = $this->collector;

        $renderer = new RenderTextFormat();
        $result = $renderer->render($registry->getMetricFamilySamples());

        header('Content-type: ' . RenderTextFormat::MIME_TYPE);
        echo $result;
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
