<?php

namespace Gustamms\PrometheusLaravel;

use Gustamms\PrometheusLaravel\Adapters\RedisClusterAdapter;
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
        if (!$storageAdapter) {
            throw new Exception('Variável PROMETHEUS_STORAGE_ADAPTER não informada');
        }

        switch ($storageAdapter) {
            case 'redis':
                $this->collector = CollectorRegistry::getDefault();
                break;
            case 'redis-cluster':
                $redisClusterServers = explode(',', env('REDIS_CLUSTER_SERVERS'));
                if (!$redisClusterServers) {
                    throw new Exception('Variável REDIS_CLUSTER_SERVERS não informada ou mal formatada');
                }

                $adapter = new RedisClusterAdapter($redisClusterServers);
                $this->collector = new CollectorRegistry($adapter);
                break;
            case 'memory':
                $this->collector = new CollectorRegistry(new InMemory());
                break;
            case 'apc':
                $this->collector = new CollectorRegistry(new APCng());
                break;
            default:
                throw new Exception("Adaptador de armazenamento $storageAdapter não suportado");
        }

        $this->namespace = config("prometheus.namespace");
    }


    public function getMetrics()
    {
        return (new RenderTextFormat())->render($this->collector->getMetricFamilySamples());
    }

    /**
     * @return RegistryInterface
     */
    public function getCollector(): RegistryInterface
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
