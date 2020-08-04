<?php

namespace Uploadcare\Interfaces\Api;

use Uploadcare\Interfaces\Conversion\ConversionRequest;
use Uploadcare\Interfaces\Conversion\ConversionStatusInterface;
use Uploadcare\Interfaces\Conversion\ConvertedItemInterface;
use Uploadcare\Interfaces\Conversion\DocumentConversionRequestInterface;
use Uploadcare\Interfaces\Conversion\VideoEncodingRequestInterface;
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
     * @param FileInfoInterface|string                                                             $file
     * @param ConversionRequest|DocumentConversionRequestInterface|VideoEncodingRequestInterface $request
     *
     * @return object
     *
     * @throws \RuntimeException
     */
    public function convertDocument($file, ConversionRequest $request);

    /**
     * @param CollectionInterface|array                                                            $collection
     * @param ConversionRequest|DocumentConversionRequestInterface|VideoEncodingRequestInterface $request
     *
     * @return BatchResponseInterface
     */
    public function batchConvertDocuments($collection, ConversionRequest $request);

    /**
     * @param ConvertedItemInterface|int $id
     *
     * @return ConversionStatusInterface
     */
    public function documentJobStatus($id);

    /**
     * @param FileInfoInterface|string $file
     * @param bool                     $throwError
     *
     * @return object
     *
     * @throws \RuntimeException
     */
    public function convertVideo($file, $throwError = true);

    /**
     * @param CollectionInterface|array $collection
     *
     * @return BatchResponseInterface
     */
    public function batchConvertVideo($collection);

    /**
     * @param $id
     *
     * @return mixed
     */
    public function videoJobStatus($id);
}
