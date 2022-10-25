<?php declare(strict_types=1);

namespace Uploadcare\Interfaces\AuthUrl;

use Uploadcare\Interfaces\File\FileInfoInterface;

interface UrlGeneratorInterface
{
    /**
     * @param FileInfoInterface|string $id
     */
    public function getUrl(AuthUrlConfigInterface $config, $id): string;
}
