<?php declare(strict_types=1);

namespace Uploadcare\Conversion;

use Uploadcare\Interfaces\Conversion\VideoEncodingRequestInterface;

final class VideoUrlBuilder
{
    /**
     * @var VideoEncodingRequestInterface
     */
    private $request;

    private $result = '/video';

    /**
     * Video Url Builder.
     * One rule: no slashes after method result.
     *
     * @param VideoEncodingRequestInterface $request
     */
    public function __construct(VideoEncodingRequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * @return string
     */
    public function __invoke(): string
    {
        return $this->result
            . \rtrim($this->resizePart(), '/')
            . \rtrim($this->qualityPart(), '/')
            . \rtrim($this->formatPart(), '/')
            . \rtrim($this->cutPart(), '/')
            . \rtrim($this->thumbsPart(), '/')
            . '/';
    }

    /**
     * @return string
     */
    private function thumbsPart(): string
    {
        return \sprintf('/-/thumbs~%s', $this->request->getThumbs());
    }

    /**
     * @return string
     */
    private function cutPart(): string
    {
        if (($start = $this->request->getStartTime()) === null) {
            return '';
        }

        $end = $this->request->getEndTime();
        if ($end === null) {
            $end = VideoEncodingRequest::DEFAULT_END_TIME;
        }

        return \sprintf('/-/cut/%s/%s', $start, $end);
    }

    /**
     * @return string
     */
    private function formatPart(): string
    {
        return \sprintf('/-/format/%s', $this->request->getTargetFormat());
    }

    /**
     * @return string
     */
    private function qualityPart(): string
    {
        if (($quality = $this->request->getQuality()) === null) {
            return '';
        }

        return \sprintf('/-/quality/%s', $quality);
    }

    /**
     * @return string
     */
    public function resizePart(): string
    {
        $hSize = $this->request->getHorizontalSize();
        $vSize = $this->request->getVerticalSize();
        if ($hSize === null) {
            $hSize = '';
        }
        if ($vSize === null) {
            $vSize = '';
        }
        if (empty($hSize) && empty($vSize)) {
            return '';
        }

        $result = \sprintf('/-/size/%sx%s', $hSize, $vSize);

        if (($resizeMode = $this->request->getResizeMode()) === null) {
            $resizeMode = VideoEncodingRequest::DEFAULT_RESIZE_MODE;
        }
        $result .= \sprintf('/%s', $resizeMode);

        return $result;
    }
}
