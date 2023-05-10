<?php

namespace Gustamms\PrometheusLaravel\Controllers;

use Gustamms\PrometheusLaravel\PrometheusCollector;
use Illuminate\Http\Response;
use Prometheus\RenderTextFormat;

class MetricController
{
    public function getMetrics(): Response
    {
        return response(
            (new PrometheusCollector())->getMetrics(),
            200,
            [
                'Content-Type' => RenderTextFormat::MIME_TYPE,
            ]
        );
    }
}
