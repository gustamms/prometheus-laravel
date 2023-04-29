<?php

namespace Gustamms\PrometheusLaravel\Controllers;

use Prometheus\RenderTextFormat;

class MetricController
{
    public function getMetrics(): void
    {
        $registry = \Prometheus\CollectorRegistry::getDefault();

        $renderer = new RenderTextFormat();
        $result = $renderer->render($registry->getMetricFamilySamples());

        header('Content-type: ' . RenderTextFormat::MIME_TYPE);
        echo $result;
    }
}
