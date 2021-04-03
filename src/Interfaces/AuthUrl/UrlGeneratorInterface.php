<?php declare(strict_types=1);

namespace Uploadcare\Interfaces\AuthUrl;

use Uploadcare\Interfaces\File\FileInfoInterface;

interface UrlGeneratorInterface
{
    /**
     * @param AuthUrlConfigInterface   $config
     * @param FileInfoInterface|string $id
     *
     * @return string
     */
    public function getUrl(AuthUrlConfigInterface $config, $id): string;
}
