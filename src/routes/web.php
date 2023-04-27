<?php

/** @var $router Laravel\Lumen\Routing\Router  */

$router->get('/metrics', [
    'as' => 'metrics', 'uses' => 'MetricController@tested'
]);