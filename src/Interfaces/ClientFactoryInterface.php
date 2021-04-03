<?php declare(strict_types=1);

namespace Uploadcare\Interfaces;

use GuzzleHttp\ClientInterface;

interface ClientFactoryInterface
{
    /**
     * @param array $options
     *
     * @return ClientInterface
     */
    public static function createClient(array $options = []): ClientInterface;
}
