<?php declare(strict_types=1);

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
     * @var int|null
     */
    private $pageNumber;

    /**
     * @param string   $targetFormat
     * @param false    $throwError
     * @param bool     $store
     * @param int|null $pageNumber
     */
    public function __construct(string $targetFormat = 'pdf', bool $throwError = false, bool $store = true, ?int $pageNumber = null)
    {
        $this->setTargetFormat($targetFormat);
        $this->setThrowError($throwError);
        $this->setStore($store);
        $this->setPageNumber($pageNumber);
    }

    /**
     * @return string
     */
    public function getTargetFormat(): string
    {
        return $this->targetFormat;
    }

    /**
     * @param string $targetFormat
     *
     * @return DocumentConversionRequest
     */
    public function setTargetFormat(string $targetFormat): self
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
    public function throwError(): bool
    {
        return $this->throwError;
    }

    /**
     * @param bool $throwError
     *
     * @return DocumentConversionRequest
     */
    public function setThrowError(bool $throwError): self
    {
        $this->throwError = $throwError;

        return $this;
    }

    /**
     * @return bool
     */
    public function store(): bool
    {
        return $this->store;
    }

    /**
     * @param bool $store
     *
     * @return DocumentConversionRequest
     */
    public function setStore(bool $store): self
    {
        $this->store = $store;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getPageNumber(): ?int
    {
        return $this->pageNumber;
    }

    /**
     * @param int|null $pageNumber
     *
     * @return DocumentConversionRequest
     */
    public function setPageNumber(?int $pageNumber): self
    {
        $this->pageNumber = $pageNumber;

        return $this;
    }
}
