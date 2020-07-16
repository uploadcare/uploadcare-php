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
class FileApiRemoteTest extends TestCase
{
    protected function setUp()
    {
        (new Dotenv())->load(\dirname(__DIR__) . '/.env.local');
    }

    public function testFileList()
    {
        $config = Configuration::create($_ENV['UPLOADCARE_PUBLIC_KEY'], $_ENV['UPLOADCARE_PRIVATE_KEY']);
        $api = new FileApi($config);

        $list = $api->listFiles(5);
        self::assertInstanceOf(FileListResponseInterface::class, $list);
    }

    public function testNextPage()
    {
        $config = Configuration::create($_ENV['UPLOADCARE_PUBLIC_KEY'], $_ENV['UPLOADCARE_PRIVATE_KEY']);
        $api = new FileApi($config);
        $list = $api->listFiles(1);
        self::assertInstanceOf(FileListResponseInterface::class, $api->nextPage($list));
    }
}
