<?php


namespace Uploadcare\Client;


use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Uploadcare\Interfaces\ClientFactoryInterface;

class HttpClientFactory implements ClientFactoryInterface
{
    /**
     * @return HttpClient
     */
    public static function createClient()
    {
        return HttpClientDiscovery::find();
    }
}
