<?php

namespace Uploadcare\Apis;

use Uploadcare\Conversion\ConversionStatus;
use Uploadcare\Exception\ConversionException;
use Uploadcare\Exception\InvalidArgumentException;
use Uploadcare\Interfaces\Api\ConversionApiInterface;
use Uploadcare\Interfaces\Conversion\ConversionRequest;
use Uploadcare\Interfaces\Conversion\ConversionStatusInterface;
use Uploadcare\Interfaces\Conversion\ConvertedItemInterface;
use Uploadcare\Interfaces\Conversion\DocumentConversionRequestInterface;
use Uploadcare\Interfaces\File\FileInfoInterface;
use Uploadcare\Interfaces\Response\BatchResponseInterface;
use Uploadcare\Interfaces\Response\ResponseProblemInterface;
use Uploadcare\Response\BatchConvertDocumentResponse;

/**
 * Conversion Api.
 */
class ConversionApi extends AbstractApi implements ConversionApiInterface
{
    /**
     * @param int|ConvertedItemInterface $id
     *
     * @return ConversionStatusInterface
     */
    public function documentJobStatus($id)
    {
        if ($id instanceof ConvertedItemInterface) {
            $id = (int) $id->getToken();
        }

        $response = $this->request('GET', \sprintf('/convert/document/status/%s/', $id));
        $result = $this->configuration->getSerializer()
            ->deserialize($response->getBody()->getContents(), ConversionStatus::class);

        if (!$result instanceof ConversionStatusInterface) {
            throw new \RuntimeException('Unable to deserialize response. Call to support');
        }

        return $result;
    }

    /**
     * @inheritDoc
     *
     * @return ConvertedItemInterface|ResponseProblemInterface
     *
     * @throws \RuntimeException|InvalidArgumentException
     */
    public function convertDocument($file, ConversionRequest $request)
    {
        if (!$request instanceof DocumentConversionRequestInterface) {
            throw new \RuntimeException(\sprintf('Request parameter must implements %s interface', DocumentConversionRequestInterface::class));
        }

        if ($file instanceof FileInfoInterface) {
            $file = $file->getUuid();
        }
        if (!\uuid_is_valid($file)) {
            throw new InvalidArgumentException(\sprintf('\'%s\' is a not valid UUID', $file));
        }

        $conversionString = $this->makeDocumentConversionUrl($file, $request);
        $result = $this->requestDocumentConversion([$conversionString]);
        if (!empty($result->getProblems())) {
            $problem = \array_values($result->getProblems())[0];

            if ($request->throwError()) {
                throw new ConversionException($problem->getReason());
            }

            return $problem;
        }

        return $result->getResult()->first();
    }

    /**
     * @inheritDoc
     */
    public function batchConvertDocuments($collection, ConversionRequest $request)
    {
        if (!$request instanceof DocumentConversionRequestInterface) {
            throw new \RuntimeException(\sprintf('Request parameter must implements %s interface', DocumentConversionRequestInterface::class));
        }

        $params = [];
        foreach ($collection as $item) {
            if ($item instanceof FileInfoInterface) {
                $item = $item->getUuid();
            }
            if (!\is_string($item) || !\uuid_is_valid($item)) {
                continue;
            }

            $params[] = $this->makeDocumentConversionUrl($item, $request);
        }

        return $this->requestDocumentConversion($params);
    }

    /**
     * @inheritDoc
     */
    public function convertVideo($file, $throwError = true)
    {
        // TODO: Implement convertVideo() method.
    }

    /**
     * @inheritDoc
     */
    public function batchConvertVideo($collection)
    {
        // TODO: Implement batchConvertVideo() method.
    }

    public function videoJobStatus($id)
    {
        // TODO: Implement method
    }

    /**
     * @param array $urls
     *
     * @return BatchResponseInterface
     */
    private function requestDocumentConversion(array $urls)
    {
        $response = $this->request('POST', 'convert/document/', [
            'body' => \json_encode($urls),
        ]);

        $result = $this->configuration->getSerializer()
            ->deserialize($response->getBody()->getContents(), BatchConvertDocumentResponse::class);

        if (!$result instanceof BatchResponseInterface) {
            throw new \RuntimeException('Unable to deserialize response. Call to support');
        }

        return $result;
    }

    /**
     * @param string                             $id      File ID
     * @param DocumentConversionRequestInterface $request
     *
     * @return string
     */
    private function makeDocumentConversionUrl($id, DocumentConversionRequestInterface $request)
    {
        $conversionString = \sprintf('%s/document/-/format/%s', $id, $request->getTargetFormat());
        if (($page = $request->getPageNumber()) !== null && \array_key_exists($request->getTargetFormat(), \array_flip(['jpg', 'png']))) {
            $conversionString = \sprintf('%s/page/%d/', $conversionString, $page);
        }

        return $conversionString;
    }
}
