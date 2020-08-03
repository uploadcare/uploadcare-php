<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Dotenv\Dotenv;
use Uploadcare\Configuration;
use Uploadcare\Interfaces\SignatureInterface;

/**
 * @group local-only
 */
class SecureAuthTest extends TestCase
{
    /**
     * @var Configuration
     */
    private $config;

    protected function setUp()
    {
        (new Dotenv())->load(__DIR__ . '/.env.local');
        $this->config = Configuration::create($_ENV['UPLOADCARE_PUBLIC_KEY'], $_ENV['UPLOADCARE_PRIVATE_KEY']);
    }

    public function testListFilesRequest()
    {
        self::assertInstanceOf(SignatureInterface::class, $this->config->getSecureSignature());

        $headers = $this->config->getAuthHeaders('GET', '/files/?limit=1&stored=true', '', 'application/json', \date_create());
        $headers['Accept'] = 'application/vnd.uploadcare-v0.6+json';

        $response = $this->config->getClient()->request('GET', 'https://api.uploadcare.com/files/?limit=1&stored=true', [
            'headers' => $headers,
        ]);

        self::assertEquals(200, $response->getStatusCode());
    }
}
