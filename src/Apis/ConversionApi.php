<?php declare(strict_types=1);

namespace Uploadcare\Apis;

use Uploadcare\Conversion\{ConversionStatus, VideoUrlBuilder};
use Uploadcare\Exception\{ConversionException, InvalidArgumentException};
use Uploadcare\Interfaces\Api\ConversionApiInterface;
use Uploadcare\Interfaces\Conversion\{ConversionRequestInterface, ConversionStatusInterface, ConvertedItemInterface,
    DocumentConversionRequestInterface, VideoEncodingRequestInterface};
use Uploadcare\Interfaces\File\FileInfoInterface;
use Uploadcare\Interfaces\Response\{BatchResponseInterface, ResponseProblemInterface};
use Uploadcare\Response\BatchConversionResponse;

/**
 * Conversion Api.
 *
 * @see https://uploadcare.com/api-refs/rest-api/v0.7.0/#tag/Conversion
 */
final class ConversionApi extends AbstractApi implements ConversionApiInterface
{
    /**
     * @param int|ConvertedItemInterface $id
     *
     * @see https://uploadcare.com/api-refs/rest-api/v0.7.0/#tag/Conversion/paths/~1convert~1document~1status~1{token}~1/get
     */
    public function documentJobStatus($id): ConversionStatusInterface
    {
        if ($id instanceof ConvertedItemInterface) {
            $id = $id->getToken();
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
     * {@inheritDoc}
     *
     * @param FileInfoInterface|string $file
     *
     * @return ConvertedItemInterface|ResponseProblemInterface
     *
     * @throws \RuntimeException|InvalidArgumentException
     *
     * @see https://uploadcare.com/api-refs/rest-api/v0.7.0/#operation/documentConvert
     */
    public function convertDocument($file, ConversionRequestInterface $request): object
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

        $conversionParams = $this->makeDocumentConversionUrl([$file], $request);
        $result = $this->requestDocumentConversion($conversionParams);
        if (!empty($result->getProblems())) {
            $problem = \array_values($result->getProblems())[0];

            if ($request->throwError()) {
                throw new ConversionException($problem->getReason() ?? 'Unknown problem');
            }

            return $problem;
        }

        return $result->getResult()->first();
    }

    /**
     * {@inheritDoc}
     */
    public function batchConvertDocuments($collection, ConversionRequestInterface $request): BatchResponseInterface
    {
        if (!$request instanceof DocumentConversionRequestInterface) {
            throw new \RuntimeException(\sprintf('Request parameter must implements %s interface', DocumentConversionRequestInterface::class));
        }

        $files = [];
        foreach ($collection as $item) {
            if ($item instanceof FileInfoInterface) {
                $item = $item->getUuid();
            }
            if (!\is_string($item) || !\uuid_is_valid($item)) {
                continue;
            }

            $files[] = $item;
        }
        $params = $this->makeDocumentConversionUrl($files, $request);

        return $this->requestDocumentConversion($params);
    }

    /**
     * @param FileInfoInterface|string                                 $file
     * @param ConversionRequestInterface|VideoEncodingRequestInterface $request
     *
     * @return ConvertedItemInterface|ResponseProblemInterface
     *
     * @see https://uploadcare.com/api-refs/rest-api/v0.7.0/#operation/videoConvert
     */
    public function convertVideo($file, ConversionRequestInterface $request): object
    {
        $file = (string) $file;

        if (!\uuid_is_valid($file)) {
            throw new InvalidArgumentException(\sprintf('File argument must be an UUID or instance of %s interface', FileInfoInterface::class));
        }

        if (!$request instanceof VideoEncodingRequestInterface) {
            throw new InvalidArgumentException(\sprintf('Conversion request of %s must implements the %s interface', __METHOD__, VideoEncodingRequestInterface::class));
        }

        $conversionUrl = $this->makeVideoConversionUrl($request);
        $fileUrl = \sprintf('%s/%s', $file, \ltrim($conversionUrl, '/'));

        try {
            $requestBody = \json_encode(['store' => $request->store(), 'paths' => [$fileUrl]], JSON_THROW_ON_ERROR);
        } catch (\Throwable $e) {
            throw new ConversionException($e->getMessage());
        }
        $response = $this->request('POST', '/convert/video/', [
            'body' => $requestBody,
        ]);
        $result = $this->configuration->getSerializer()
            ->deserialize($response->getBody()->getContents(), BatchConversionResponse::class);

        if (!$result instanceof BatchResponseInterface) {
            throw new \RuntimeException('Unable to deserialize response. Call to support');
        }

        if (!empty($result->getProblems())) {
            $problem = \array_values($result->getProblems())[0];
            $reason = $problem instanceof ResponseProblemInterface ? $problem->getReason() : 'Unknown problem';

            if ($request->throwError()) {
                throw new ConversionException($reason ?? 'Unknown problem');
            }

            return $problem;
        }

        return $result->getResult()->first();
    }

    /**
     * {@inheritDoc}
     */
    public function batchConvertVideo($collection, ConversionRequestInterface $request): BatchResponseInterface
    {
        if (!$request instanceof VideoEncodingRequestInterface) {
            throw new InvalidArgumentException(\sprintf('Conversion request of %s must implements the %s interface', __METHOD__, VideoEncodingRequestInterface::class));
        }
        $conversionUrl = $this->makeVideoConversionUrl($request);

        $urls = [];
        foreach ($collection as $item) {
            if ($item instanceof FileInfoInterface) {
                $item = $item->getUuid();
            }
            if (!\is_string($item) || !\uuid_is_valid($item)) {
                continue;
            }

            $urls[] = \sprintf('%s/%s', $item, \ltrim($conversionUrl, '/'));
        }
        if (empty($urls)) {
            throw new InvalidArgumentException('Collection has no valid files or uuid\'s');
        }
        try {
            $requestBody = \json_encode(['store' => $request->store(), 'paths' => $urls], JSON_THROW_ON_ERROR);
        } catch (\Throwable $e) {
            throw new \RuntimeException($e->getMessage());
        }

        $response = $this->request('POST', '/convert/video/', [
            'body' => $requestBody,
        ]);

        $result = $this->configuration->getSerializer()
            ->deserialize($response->getBody()->getContents(), BatchConversionResponse::class);

        if (!$result instanceof BatchResponseInterface) {
            throw new \RuntimeException('Unable to deserialize response. Call to support');
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     *
     * @see https://uploadcare.com/api-refs/rest-api/v0.7.0/#operation/videoConvertStatus
     */
    public function videoJobStatus($id): ConversionStatusInterface
    {
        if ($id instanceof ConvertedItemInterface) {
            $id = $id->getToken();
        }

        $url = \sprintf('/convert/video/status/%s/', $id);
        $response = $this->request('GET', $url);

        $result = $this->configuration->getSerializer()
            ->deserialize($response->getBody()->getContents(), ConversionStatus::class);

        if (!$result instanceof ConversionStatusInterface) {
            throw new \RuntimeException('Unable to deserialize response. Call to support');
        }

        return $result;
    }

    protected function makeVideoConversionUrl(VideoEncodingRequestInterface $request): string
    {
        $builder = new VideoUrlBuilder($request);

        return $builder();
    }

    private function requestDocumentConversion(array $conversionParams): BatchResponseInterface
    {
        try {
            $parameters = \json_encode($conversionParams, JSON_THROW_ON_ERROR);
        } catch (\Throwable $e) {
            throw new \RuntimeException($e->getMessage());
        }

        $response = $this->request('POST', 'convert/document/', [
            'body' => $parameters,
        ]);

        $result = $this->configuration->getSerializer()
            ->deserialize($response->getBody()->getContents(), BatchConversionResponse::class);

        if (!$result instanceof BatchResponseInterface) {
            throw new \RuntimeException('Unable to deserialize response. Call to support');
        }

        return $result;
    }

    /**
     * @param array $ids File ID's
     */
    private function makeDocumentConversionUrl(array $ids, DocumentConversionRequestInterface $request): array
    {
        $patch = [];
        foreach ($ids as $id) {
            $conversionString = \sprintf('%s/document/-/format/%s', $id, $request->getTargetFormat());
            if (($page = $request->getPageNumber()) !== null && \array_key_exists($request->getTargetFormat(), \array_flip(['jpg', 'png']))) {
                $patch[] = \sprintf('%s/-/page/%d/', $conversionString, $page);
            } else {
                $patch[] = \sprintf('%s/', \rtrim($conversionString, '/'));
            }
        }

        return [
            'paths' => $patch,
            'store' => $request->store(),
        ];
    }
}
