<?php declare(strict_types=1);

namespace Uploadcare\Client;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Uploadcare\Interfaces\ClientFactoryInterface;

/**
 * Http Client Factory.
 */
class ClientFactory implements ClientFactoryInterface
{
    /**
     * @param array $options
     *
     * @return ClientInterface
     */
    public static function createClient(array $options = []): ClientInterface
    {
        return new Client($options);
    }
}
