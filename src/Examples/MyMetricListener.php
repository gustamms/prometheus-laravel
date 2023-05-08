<?php

namespace Picpay\Otel\Examples;

use Picpay\Otel\Metrics\Counter;

class MyMetricListener
{
    public function handle(MyEvent $event): void
    {
        Counter::add(1, 'metric_name', [
            'label1'=>'value1',
            'label2'=>'value2'
        ], 'description');
    }
}