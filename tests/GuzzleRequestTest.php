<?php

namespace Tests;

use Faker\Factory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Uploadcare\Configuration;
use Uploadcare\Interfaces\UploaderInterface;

class GuzzleRequestTest extends TestCase
{
    public function testIsUserAgentSet()
    {
        $conf = Configuration::create('demo-public-key', 'demo-private-key');
        $file = fopen('php://memory', 'rb+');
        \fwrite($file, Factory::create()->sentence);
        \rewind($file);

        $data = [
            'headers' => $conf->getHeaders(),
            'multipart' => [
                [
                    'name' => UploaderInterface::UPLOADCARE_STORE_KEY,
                    'contents' => 'auto',
                ],
                [
                    'name' => UploaderInterface::UPLOADCARE_PUB_KEY_KEY,
                    'contents' => 'demo-key',
                ],
                [
                    'name' => 'file',
                    'contents' => $file,
                    'filename' => 'some-file-name',
                ],
            ],
        ];

        $uri = 'https://httpbin.org/post';

        $response = $conf->getClient()
            ->request('POST', $uri, $data);
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $result = \json_decode($response->getBody()->getContents(), true);

        $this->assertArrayHasKey('headers', $result);
        $this->assertEquals($conf->getHeaders()['User-Agent'], $result['headers']['User-Agent']);
    }
}
