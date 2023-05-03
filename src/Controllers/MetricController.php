<?php

namespace Gustamms\PrometheusLaravel\Controllers;

use Gustamms\PrometheusLaravel\PrometheusCollector;

class MetricController
{
    public function getMetrics(): void
    {
        (new PrometheusCollector())->renderMetrics();
    }
}
