<?php

namespace Tests\Api;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Dotenv\Dotenv;
use Uploadcare\Configuration;
use Uploadcare\FileApi;
use Uploadcare\Interfaces\Response\FileListResponseInterface;

/**
 * @group local-only
 */
class FileApiTest extends TestCase
{
    protected function setUp()
    {
        (new Dotenv())->load(\dirname(__DIR__) . '/.env.local');
    }

    public function testFileList()
    {
        $config = Configuration::create($_ENV['UPLOADCARE_PUBLIC_KEY'], $_ENV['UPLOADCARE_PRIVATE_KEY']);
        $api = new FileApi($config);

        $list = $api->listFiles();
        self::assertInstanceOf(FileListResponseInterface::class, $list);
    }
}
