<?php declare(strict_types=1);

namespace Uploadcare\Apis;

use Uploadcare\Conversion\ConversionStatus;
use Uploadcare\Conversion\VideoUrlBuilder;
use Uploadcare\Exception\ConversionException;
use Uploadcare\Exception\InvalidArgumentException;
use Uploadcare\Interfaces\Api\ConversionApiInterface;
use Uploadcare\Interfaces\Conversion\ConversionRequestInterface;
use Uploadcare\Interfaces\Conversion\ConversionStatusInterface;
use Uploadcare\Interfaces\Conversion\ConvertedItemInterface;
use Uploadcare\Interfaces\Conversion\DocumentConversionRequestInterface;
use Uploadcare\Interfaces\Conversion\VideoEncodingRequestInterface;
use Uploadcare\Interfaces\File\FileInfoInterface;
use Uploadcare\Interfaces\Response\BatchResponseInterface;
use Uploadcare\Interfaces\Response\ResponseProblemInterface;
use Uploadcare\Response\BatchConversionResponse;

/**
 * Conversion Api.
 *
 * @see https://uploadcare.com/api-refs/rest-api/v0.6.0/#tag/Conversion
 */
final class ConversionApi extends AbstractApi implements ConversionApiInterface
{
    /**
     * @param int|ConvertedItemInterface $id
     *
     * @return ConversionStatusInterface
     *
     * @see https://uploadcare.com/api-refs/rest-api/v0.6.0/#tag/Conversion/paths/~1convert~1document~1status~1{token}~1/get
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
     * @return ConvertedItemInterface|ResponseProblemInterface
     *
     * @throws \RuntimeException|InvalidArgumentException
     *
     * @see https://uploadcare.com/api-refs/rest-api/v0.6.0/#operation/documentConvert
     */
    public function convertDocument($file, ConversionRequestInterface $request)
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
                throw new ConversionException($problem->getReason());
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
     * @see https://uploadcare.com/api-refs/rest-api/v0.6.0/#operation/videoConvert
     */
    public function convertVideo($file, ConversionRequestInterface $request)
    {
        if ($file instanceof FileInfoInterface) {
            $file = $file->getUuid();
        }
        if (!\is_string($file) || !\uuid_is_valid($file)) {
            throw new InvalidArgumentException(\sprintf('File argument must be an UUID or instance of %s interface', FileInfoInterface::class));
        }

        if (!$request instanceof VideoEncodingRequestInterface) {
            throw new InvalidArgumentException(\sprintf('Conversion request of %s must implements the %s interface', __METHOD__, VideoEncodingRequestInterface::class));
        }

        $conversionUrl = $this->makeVideoConversionUrl($request);
        $fileUrl = \sprintf('%s/%s', $file, \ltrim($conversionUrl, '/'));

        $requestBody = [
            'store' => $request->store(),
            'paths' => [$fileUrl],
        ];
        $response = $this->request('POST', '/convert/video/', [
            'body' => \json_encode($requestBody),
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
                throw new ConversionException($reason);
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
        $requestBody = [
            'store' => $request->store(),
            'paths' => $urls,
        ];

        $response = $this->request('POST', '/convert/video/', [
            'body' => \json_encode($requestBody),
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
     * @see https://uploadcare.com/api-refs/rest-api/v0.6.0/#operation/videoConvertStatus
     */
    public function videoJobStatus($id)
    {
        if ($id instanceof ConvertedItemInterface) {
            $id = $id->getToken();
        }
        if (!\is_int($id)) {
            throw new InvalidArgumentException(\sprintf('Conversion result ID must be a number, %s given', \gettype($id)));
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

    /**
     * @param VideoEncodingRequestInterface $request
     *
     * @return string
     */
    protected function makeVideoConversionUrl(VideoEncodingRequestInterface $request): string
    {
        $builder = new VideoUrlBuilder($request);

        return $builder();
    }

    /**
     * @param array $conversionParams
     *
     * @return BatchResponseInterface
     */
    private function requestDocumentConversion(array $conversionParams): BatchResponseInterface
    {
        $response = $this->request('POST', 'convert/document/', [
            'body' => \json_encode($conversionParams),
        ]);

        $result = $this->configuration->getSerializer()
            ->deserialize($response->getBody()->getContents(), BatchConversionResponse::class);

        if (!$result instanceof BatchResponseInterface) {
            throw new \RuntimeException('Unable to deserialize response. Call to support');
        }

        return $result;
    }

    /**
     * @param array                              $ids     File ID's
     * @param DocumentConversionRequestInterface $request
     *
     * @return array
     */
    private function makeDocumentConversionUrl($ids, DocumentConversionRequestInterface $request): array
    {
        $patch = [];
        foreach ($ids as $id) {
            $conversionString = \sprintf('%s/document/-/format/%s', $id, $request->getTargetFormat());
            if (($page = $request->getPageNumber()) !== null && \array_key_exists($request->getTargetFormat(), \array_flip(['jpg', 'png']))) {
                $patch[] = \sprintf('%s/-/page/%d/', $conversionString, $page);
            } else {
                $patch[] = $conversionString;
            }
        }

        return [
            'paths' => $patch,
            'store' => $request->store(),
        ];
    }
}
