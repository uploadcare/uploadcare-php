<?php

namespace Tests\Uploader;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Uploadcare\Configuration;
use Uploadcare\Security\Signature;
use Uploadcare\Serializer\Serializer;
use Uploadcare\Serializer\SnackCaseConverter;
use Uploadcare\Uploader;

class UploaderMethodsTest extends TestCase
{
    /**
     * @param ClientInterface $client
     *
     * @return Configuration
     */
    protected function makeConfiguration(ClientInterface $client)
    {
        $sign = new Signature('demo-private-key');
        $serializer = new Serializer(new SnackCaseConverter());

        return new Configuration('demo-public-key', $sign, $client, $serializer);
    }

    /**
     * @param ResponseInterface|GuzzleException $response
     *
     * @return Client
     */
    protected function makeClient($response)
    {
        $fileResponse = new Response(200, ['Content-Type' => 'application/json'], \file_get_contents(\dirname(__DIR__) . '/_data/uploaded-file.json'));
        $handler = new MockHandler([$response, $fileResponse]);

        return new Client(['handler' => HandlerStack::create($handler)]);
    }

    /**
     * @param array $responseBody
     *
     * @return Uploader
     */
    protected function makeUploaderWithResponse(array $responseBody)
    {
        $response = new Response(200, ['Content-Type' => 'application/json'], \json_encode($responseBody));
        $client = $this->makeClient($response);
        $config = $this->makeConfiguration($client);

        return new Uploader($config);
    }
}
