<?php declare(strict_types=1);

namespace Tests\File;

use PHPUnit\Framework\TestCase;
use Uploadcare\File\File;
use Uploadcare\File\FileCollection;
use Uploadcare\Interfaces\File\FileInfoInterface;

class FileCollectionTest extends TestCase
{
    protected function generateFile(array $data): FileInfoInterface
    {
        $file = new File();
        $properties = (new \ReflectionClass(File::class))->getProperties(\ReflectionProperty::IS_PRIVATE);

        foreach ($properties as $property) {
            if (\array_key_exists($property->getName(), $data)) {
                $property->setAccessible(true);
                $property->setValue($file, $data[$property->getName()]);
            }
        }

        return $file;
    }

    protected function filesArray(int $count = 5): array
    {
        $files = [];
        for ($i = 0; $i < $count; ++$i) {
            $id = \uuid_create();
            $files[] = $this->generateFile(['uuid' => $id]);
        }

        return $files;
    }

    public function testCollectionCreation(): void
    {
        $files = $this->filesArray();
        $collection = new FileCollection($files);
        self::assertFalse($collection->isEmpty());
    }

    public function testGetIterator(): void
    {
        $c = new FileCollection();
        self::assertIsIterable($c->getIterator());
    }

    public function testOffsetExists(): void
    {
        $collection = new FileCollection($this->filesArray(10));
        self::assertTrue($collection->offsetExists(0));
        self::assertFalse($collection->offsetExists(10));
    }

    public function testOffsetGet(): void
    {
        $collection = new FileCollection($this->filesArray(10));
        self::assertInstanceOf(FileInfoInterface::class, $collection->offsetGet(0));
    }

    public function testOffsetSet(): void
    {
        $newFile = $this->generateFile(['uuid' => \uuid_create()]);
        $collection = new FileCollection($this->filesArray(10));
        $oldFile = $collection->offsetGet(0);
        $collection->offsetSet(0, $newFile);
        self::assertNotEquals($oldFile, $collection->offsetGet(0));
    }

    public function testOffsetUnset(): void
    {
        $collection = new FileCollection($this->filesArray(1));
        $collection->offsetUnset(0);
        self::assertTrue($collection->isEmpty());
    }

    public function testCount(): void
    {
        $collection = new FileCollection($this->filesArray(1));
        self::assertEquals(1, $collection->count());
        self::assertCount(1, $collection);
    }

    public function testAdd(): void
    {
        $collection = new FileCollection($this->filesArray(2));
        self::assertCount(2, $collection);
        $newFile = $this->generateFile(['uuid' => \uuid_create()]);
        self::assertTrue($collection->add($newFile));
        self::assertCount(3, $collection);
    }

    public function testClear(): void
    {
        $collection = new FileCollection($this->filesArray(10));
        self::assertCount(10, $collection);
        $collection->clear();
        self::assertCount(0, $collection);
        self::assertEmpty($collection);
    }

    public function testContains(): void
    {
        $file = $this->generateFile(['uuid' => \uuid_create()]);
        $collection = new FileCollection($this->filesArray(1));
        self::assertFalse($collection->contains($file));
    }

    public function testIsEmpty(): void
    {
        $collection = new FileCollection($this->filesArray(1));
        self::assertFalse($collection->isEmpty());
        self::assertTrue((new FileCollection())->isEmpty());
    }

    public function testRemove(): void
    {
        $file = $this->generateFile(['uuid' => \uuid_create()]);
        $collection = new FileCollection($this->filesArray(1));
        self::assertNull($collection->remove(100));
        $collection->add($file);
        self::assertEquals($file, $collection->remove(1));
    }

    public function testRemoveElement(): void
    {
        $file = $this->generateFile(['uuid' => \uuid_create()]);
        $collection = new FileCollection($this->filesArray(1));
        $collection->add($file);
        self::assertCount(2, $collection);
        self::assertTrue($collection->removeElement($file));
        self::assertFalse($collection->contains($file));
    }

    public function testGet(): void
    {
        $collection = new FileCollection($this->filesArray(1));
        self::assertNull($collection->get(100));
        self::assertInstanceOf(FileInfoInterface::class, $collection->get(0));
    }

    public function testGetKeys(): void
    {
        $collection = new FileCollection($this->filesArray(10));
        $keys = $collection->getKeys();
        self::assertCount(10, $keys);
    }

    public function testGetValues(): void
    {
        $files = $this->filesArray(10);
        $collection = new FileCollection($files);
        self::assertEquals($files, $collection->toArray());
        self::assertEquals($files, $collection->getValues());
    }

    public function testAccess(): void
    {
        $files = $this->filesArray(5);
        $collection = new FileCollection($files);
        self::assertEquals($collection->last(), $files[4]);
        self::assertEquals($collection->first(), $files[0]);
        self::assertInstanceOf(FileInfoInterface::class, $collection->next());
        self::assertNotEquals($collection->current(), $files[0]);
        self::assertNotEquals(0, $collection->key());
    }

    public function testFilter(): void
    {
        $file = $this->generateFile(['uuid' => \uuid_create()]);
        $collection = new FileCollection($this->filesArray(10));
        $collection->add($file);
        $filtered = $collection->filter(static function (FileInfoInterface $fileInfo) use ($file) {
            return $fileInfo->getUuid() === $file->getUuid();
        });
        self::assertNotEmpty($filtered);
        self::assertEquals($file, $filtered->first());
    }

    public function testMap(): void
    {
        $collection = new FileCollection($this->filesArray(2));
        $mapped = $collection->map(static function (File $file) {
            return $file->setMimeType('application/pdf');
        });

        self::assertEquals('application/pdf', $mapped->current()->getMimeType());
        self::assertEquals('application/pdf', $mapped->first()->getMimeType());
        self::assertEquals('application/pdf', $mapped->last()->getMimeType());
    }

    public function testIndexOf(): void
    {
        $collection = new FileCollection($this->filesArray(2));
        $file = $this->generateFile(['uuid' => \uuid_create()]);
        $collection->add($file);
        self::assertEquals(2, $collection->indexOf($file));
    }
}
