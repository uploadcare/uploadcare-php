<?php

namespace Uploadcare\AuthUrl;

use Uploadcare\Exception\InvalidArgumentException;
use Uploadcare\Interfaces\AuthUrl\AuthUrlConfigInterface;
use Uploadcare\Interfaces\AuthUrl\UrlGeneratorInterface;
use Uploadcare\Interfaces\File\FileInfoInterface;

abstract class AbstractUrlGenerator implements UrlGeneratorInterface
{
    /**
     * @return string
     */
    abstract protected function getTemplate();

    /**
     * @inheritDoc
     */
    public function getUrl(AuthUrlConfigInterface $config, $id)
    {
        if ($id instanceof FileInfoInterface) {
            $id = $id->getUuid();
        }

        if (!\uuid_is_valid($id)) {
            throw new InvalidArgumentException(\sprintf('UUID %s is not valid', (\is_string($id) ? $id : \gettype($id))));
        }

        if (($ts = $config->getTimeStamp()) === null || ($token = $config->getToken()) === null) {
            throw new InvalidArgumentException('You must set the CDN token and timestamp first');
        }

        return \strtr($this->getTemplate(), [
            '{cdn}' => $config->getCdnUrl(),
            '{uuid}' => $id,
            '{timestamp}' => $ts,
            '{token}' => $token,
        ]);
    }
}
