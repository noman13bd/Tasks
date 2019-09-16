<?php
namespace Task;

use Task\Api;

abstract class Route
{
    /**
     * @var \Slim\Slim
     */
    protected $api;

    public function __construct(Api $api)
    {
        $this->api = $api;
    }
}
