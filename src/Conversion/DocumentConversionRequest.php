<?php

namespace Uploadcare\Conversion;

use Uploadcare\Exception\InvalidArgumentException;
use Uploadcare\Interfaces\Conversion\DocumentConversionRequestInterface;

class DocumentConversionRequest implements DocumentConversionRequestInterface
{
    /**
     * @var string[] Support formats
     */
    protected static $formats = ['doc', 'docx', 'xls', 'xlsx', 'odt', 'ods', 'rtf', 'txt', 'pdf', 'jpg', 'png'];

    /**
     * @var string
     */
    private $targetFormat;

    /**
     * @var bool
     */
    private $throwError;

    /**
     * @var bool
     */
    private $store;

    /**
     * @var int
     */
    private $pageNumber;

    /**
     * @param string $targetFormat
     * @param false  $throwError
     * @param bool   $store
     * @param int    $pageNumber
     */
    public function __construct($targetFormat = 'pdf', $throwError = false, $store = true, $pageNumber = 1)
    {
        $this->setTargetFormat((string) $targetFormat);
        $this->setThrowError((bool) $throwError);
        $this->setStore((bool) $store);
        $this->setPageNumber((int) $pageNumber);
    }

    /**
     * @return string
     */
    public function getTargetFormat()
    {
        return $this->targetFormat;
    }

    /**
     * @param string $targetFormat
     *
     * @return DocumentConversionRequest
     */
    public function setTargetFormat($targetFormat)
    {
        if (!\array_key_exists($targetFormat, \array_flip(self::$formats))) {
            throw new InvalidArgumentException(\sprintf('Format \'%s\' not supported. Supports formats are %s', $targetFormat, \implode(', ', self::$formats)));
        }

        $this->targetFormat = $targetFormat;

        return $this;
    }

    /**
     * @return bool
     */
    public function throwError()
    {
        return $this->throwError;
    }

    /**
     * @param bool $throwError
     *
     * @return DocumentConversionRequest
     */
    public function setThrowError($throwError)
    {
        $this->throwError = $throwError;

        return $this;
    }

    /**
     * @return bool
     */
    public function store()
    {
        return $this->store;
    }

    /**
     * @param bool $store
     *
     * @return DocumentConversionRequest
     */
    public function setStore($store)
    {
        $this->store = $store;

        return $this;
    }

    /**
     * @return int
     */
    public function getPageNumber()
    {
        return $this->pageNumber;
    }

    /**
     * @param int $pageNumber
     *
     * @return DocumentConversionRequest
     */
    public function setPageNumber($pageNumber)
    {
        $this->pageNumber = $pageNumber;

        return $this;
    }
}
