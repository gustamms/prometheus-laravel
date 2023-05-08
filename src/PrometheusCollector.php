<?php

namespace Gustamms\PrometheusLaravel;

use Prometheus\CollectorRegistry;
use Prometheus\RegistryInterface;
use Prometheus\RenderTextFormat;
use Prometheus\Storage\APCng;
use Prometheus\Storage\InMemory;
use Exception;

class PrometheusCollector
{
    private RegistryInterface $collector;
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
            case 'apc':
                $this->collector = new CollectorRegistry(new APCng());
                break;
        }

        $this->namespace = config("prometheus.namespace");
    }

    public function getMetrics()
    {
        return (new RenderTextFormat())->render($this->collector->getMetricFamilySamples());
    }

    /**
     * @return CollectorRegistry|RegistryInterface
     */
    public function getCollector(): RegistryInterface|CollectorRegistry
    {
        return $this->collector;
    }

    /**
     * @return string
     */
    public function getNamespace(): string
    {
        return $this->namespace;
    }
}
