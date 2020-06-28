<?php

namespace Uploadcare\Interfaces;

use GuzzleHttp\ClientInterface;

interface ClientFactoryInterface
{
    /**
     * @param array $options
     *
     * @return ClientInterface
     */
    public static function createClient(array $options = []);
}
