<?php declare(strict_types=1);

namespace Tests\Metadata;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Dotenv\Dotenv;
use Uploadcare\Api;
use Uploadcare\Configuration;
use Uploadcare\File;

/**
 * @group local-only
 */
class MetadataRealApiTest extends TestCase
{
    protected function setUp(): void
    {
        (new Dotenv())->load(\dirname(__DIR__) . '/.env.local');
    }

    public function testFileMetadataLoad(): void
    {
        $config = Configuration::create($_ENV['UPLOADCARE_PUBLIC_KEY'], $_ENV['UPLOADCARE_PRIVATE_KEY']);
        $api = new Api($config);
        $list = $api->file()->listFiles(1);
        $file = $list->getResults()->current();
        self::assertInstanceOf(File::class, $file);

        $metadata = $file->getMetadata();
        self::assertInstanceOf(File\Metadata::class, $metadata);

        $key = 'current_timestamp';
        $value = \date_create()->format(\DateTimeInterface::ATOM);

        $api->metadata()->setKey($file, $key, $value);

        self::assertSame($value, $file->getMetadata()->offsetGet($key));
    }
}
