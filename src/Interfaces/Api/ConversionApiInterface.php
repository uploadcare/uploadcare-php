<?php declare(strict_types=1);

namespace Uploadcare\Interfaces\Api;

use Uploadcare\Interfaces\Conversion\ConversionRequestInterface;
use Uploadcare\Interfaces\Conversion\ConversionStatusInterface;
use Uploadcare\Interfaces\Conversion\ConvertedItemInterface;
use Uploadcare\Interfaces\Conversion\DocumentConversionRequestInterface;
use Uploadcare\Interfaces\File\CollectionInterface;
use Uploadcare\Interfaces\File\FileInfoInterface;
use Uploadcare\Interfaces\Response\BatchResponseInterface;

/**
 * Conversion API.
 */
interface ConversionApiInterface
{
    /**
     * Request a document conversion.
     *
     * @param FileInfoInterface|string                                      $file
     * @param ConversionRequestInterface|DocumentConversionRequestInterface $request
     *
     * @return object
     *
     * @throws \RuntimeException
     */
    public function convertDocument($file, ConversionRequestInterface $request);

    /**
     * @param CollectionInterface|array                                     $collection
     * @param ConversionRequestInterface|DocumentConversionRequestInterface $request
     *
     * @return BatchResponseInterface
     */
    public function batchConvertDocuments($collection, ConversionRequestInterface $request): BatchResponseInterface;

    /**
     * @param ConvertedItemInterface|int $id
     *
     * @return ConversionStatusInterface
     */
    public function documentJobStatus($id): ConversionStatusInterface;

    /**
     * @param FileInfoInterface|string   $file
     * @param ConversionRequestInterface $request
     *
     * @return object
     *
     * @throws \RuntimeException
     */
    public function convertVideo($file, ConversionRequestInterface $request);

    /**
     * @param CollectionInterface|array  $collection
     * @param ConversionRequestInterface $request
     *
     * @return BatchResponseInterface
     */
    public function batchConvertVideo($collection, ConversionRequestInterface $request): BatchResponseInterface;

    /**
     * @param int|ConvertedItemInterface $id
     *
     * @return mixed
     */
    public function videoJobStatus($id);
}
