<?php

namespace Gustamms\PrometheusLaravel;

use Predis\Client;
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
                $this->collector = new CollectorRegistry(
                    new \Prometheus\Storage\Redis(
                        [
                            'client' => 'phpredis',
                            'cluster' => true,
                            'default' => [
                                'host' => env('PROMETHEUS_REDIS_HOST'),
                                'port' => env('PROMETHEUS_REDIS_PORT'),
                                'database' => 0,
                                'read_timeout' => 60,
                            ]
                        ]
                    )
                );
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
