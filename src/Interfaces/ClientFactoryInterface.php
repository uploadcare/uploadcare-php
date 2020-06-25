<?php


namespace Uploadcare\Interfaces;


use Http\Client\HttpClient;

interface ClientFactoryInterface
{
    /**
     * @return HttpClient
     */
    public static function createClient();
}
