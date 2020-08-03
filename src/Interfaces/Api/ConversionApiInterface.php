<?php

namespace Uploadcare\Interfaces\Api;

use Uploadcare\Interfaces\Conversion\ConversionRequest;
use Uploadcare\Interfaces\Conversion\DocumentConversionRequestInterface;
use Uploadcare\Interfaces\Conversion\VideoConversionRequestInterface;
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
     * @param ConversionRequest|DocumentConversionRequestInterface|VideoConversionRequestInterface $request
     *
     * @return object
     *
     * @throws \RuntimeException
     */
    public function convertDocument($file, ConversionRequest $request);

    /**
     * @param CollectionInterface|array                                                            $collection
     * @param ConversionRequest|DocumentConversionRequestInterface|VideoConversionRequestInterface $request
     *
     * @return BatchResponseInterface
     */
    public function batchConvertDocuments($collection, ConversionRequest $request);

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
}
