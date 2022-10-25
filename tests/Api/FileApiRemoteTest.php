<?php declare(strict_types=1);

namespace Tests\Api;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Dotenv\Dotenv;
use Uploadcare\Apis\FileApi;
use Uploadcare\Configuration;
use Uploadcare\Interfaces\Response\ListResponseInterface;

/**
 * @group local-only
 */
class FileApiRemoteTest extends TestCase
{
    protected function setUp(): void
    {
        (new Dotenv())->load(\dirname(__DIR__) . '/.env.local');
    }

    public function testFileList(): void
    {
        $config = Configuration::create($_ENV['UPLOADCARE_PUBLIC_KEY'], $_ENV['UPLOADCARE_PRIVATE_KEY']);
        $api = new FileApi($config);

        $list = $api->listFiles(5);
        self::assertInstanceOf(ListResponseInterface::class, $list);
    }

    public function testNextPage(): void
    {
        $config = Configuration::create($_ENV['UPLOADCARE_PUBLIC_KEY'], $_ENV['UPLOADCARE_PRIVATE_KEY']);
        $api = new FileApi($config);
        $list = $api->listFiles(1);
        self::assertInstanceOf(ListResponseInterface::class, $api->nextPage($list));
    }
}
