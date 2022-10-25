<?php declare(strict_types=1);

namespace Tests\Conversion;

use PHPUnit\Framework\TestCase;
use Uploadcare\Conversion\VideoEncodingRequest;
use Uploadcare\Conversion\VideoUrlBuilder;

class VideoUrlBuilderTest extends TestCase
{
    public function generateRequests(): array
    {
        $default = '-/format/mp4/-/thumbs~1/';

        return [
            [
                '/video/' . $default,
                new VideoEncodingRequest(),
            ],
            [
                '/video/-/size/720x540/preserve_ratio/' . $default,
                (new VideoEncodingRequest())
                    ->setHorizontalSize(720)
                    ->setVerticalSize(540),
            ],
            [
                '/video/-/size/800x600/change_ratio/' . $default,
                (new VideoEncodingRequest())
                    ->setHorizontalSize(800)
                    ->setVerticalSize(600)
                    ->setResizeMode('change_ratio'),
            ],
            [
                '/video/-/size/800x/preserve_ratio/-/quality/better/' . $default,
                (new VideoEncodingRequest())
                    ->setQuality('better')
                    ->setHorizontalSize(800),
            ],
            [
                '/video/-/quality/normal/' . $default,
                (new VideoEncodingRequest())
                    ->setQuality('normal'),
            ],
            [
                '/video/-/format/ogg/-/thumbs~1/',
                (new VideoEncodingRequest())
                    ->setTargetFormat('ogg'),
            ],
            [
                '/video/-/quality/normal/-/format/ogg/-/thumbs~1/',
                (new VideoEncodingRequest())
                    ->setQuality('normal')
                    ->setTargetFormat('ogg'),
            ],
            [
                '/video/-/format/mp4/-/cut/80:00.0/end/-/thumbs~1/',
                (new VideoEncodingRequest())
                    ->setStartTime('80:00.0')
                    ->setTargetFormat('mp4')
                    ->setThumbs(1),
            ],
            [
                '/video/-/format/mp4/-/thumbs~20/',
                (new VideoEncodingRequest())
                    ->setThumbs(20),
            ],
            [
                '/video/-/format/mp4/-/thumbs~' . VideoEncodingRequest::MAX_THUMBS . '/',
                (new VideoEncodingRequest())
                    ->setThumbs(120),
            ],
        ];
    }

    /**
     * @dataProvider generateRequests
     *
     * @param $url
     */
    public function testVideoUrlGeneration(string $url, VideoEncodingRequest $request): void
    {
        $builder = new VideoUrlBuilder($request);
        self::assertEquals($builder(), $url);
    }
}
