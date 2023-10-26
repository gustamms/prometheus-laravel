<?php

namespace Gustamms\PrometheusLaravel\Adapters;

use Prometheus\Storage\Adapter;
use Predis\Client;
use Prometheus\Storage\Redis;

class RedisClusterAdapter extends Redis implements Adapter
{
    private Client $redisClusterClient;

    public function __construct(array $redisHosts)
    {
        $options = [
            'cluster' => 'redis',
            'parameters' => [
                'password' => env('REDIS_AUTH')
            ],
        ];

        $this->redisClusterClient = new Client($redisHosts, $options);
        parent::__construct($options);
    }

    public function collect(bool $sortMetrics = true): array
    {
        $metrics = [];

        $metricPrefix = 'metric_';

        $keys = $this->redisClusterClient->keys("$metricPrefix*");

        foreach ($keys as $key) {
            $metricName = str_replace($metricPrefix, '', $key);

            $value = $this->redisClusterClient->get($key);

            $labelStr = strstr($metricName, '{');
            $labels = $labelStr ? json_decode($labelStr, true) : [];
            $metricName = $labelStr ? strstr($metricName, '{', true) : $metricName;

            $metrics[] = [
                'name' => $metricName,
                'labels' => $labels,
                'value' => $value
            ];
        }

        if ($sortMetrics) {
            usort($metrics, function ($a, $b) {
                return strcmp($a['name'], $b['name']);
            });
        }

        return $metrics;
    }


    public function updateCounter(array $data): void
    {
        $metricName = $data['name'];
        $labels = $data['labels'];
        $value = $data['value'];

        $key = $this->generateKey($metricName, $labels);
        $this->redisClusterClient->incrby($key, $value);
    }

    private function generateKey($metricName, $labels): string
    {
        ksort($labels);
        $labelStr = json_encode($labels);
        $safeKey = preg_replace('/[^a-zA-Z0-9_]/', '_', $metricName . '_' . $labelStr);
        return $safeKey;
    }
}
