<?php declare(strict_types=1);

namespace Uploadcare\Client;

use GuzzleHttp\{Client, ClientInterface};
use Uploadcare\Interfaces\ClientFactoryInterface;

/**
 * Http Client Factory.
 */
class ClientFactory implements ClientFactoryInterface
{
    public static function createClient(array $options = []): ClientInterface
    {
        return new Client($options);
    }
}
