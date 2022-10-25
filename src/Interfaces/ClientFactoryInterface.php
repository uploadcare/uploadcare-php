<?php declare(strict_types=1);

namespace Uploadcare\Interfaces;

use GuzzleHttp\ClientInterface;

interface ClientFactoryInterface
{
    public static function createClient(array $options = []): ClientInterface;
}
