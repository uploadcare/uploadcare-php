<?php declare(strict_types=1);

namespace Uploadcare\Conversion;

use Uploadcare\Exception\InvalidArgumentException;
use Uploadcare\Interfaces\Conversion\DocumentConversionRequestInterface;

class DocumentConversionRequest implements DocumentConversionRequestInterface
{
    /**
     * @var string[] Supporting formats
     */
    protected static array $formats = ['doc', 'docx', 'xls', 'xlsx', 'odt', 'ods', 'rtf', 'txt', 'pdf', 'jpg', 'png'];

    private string $targetFormat = 'pdf';
    private bool $throwError = false;
    private bool $store = true;
    private ?int $pageNumber = null;
    private bool $saveToGroup = false;

    public function __construct(string $targetFormat = 'pdf', bool $throwError = false, bool $store = true, ?int $pageNumber = null)
    {
        $this->setTargetFormat($targetFormat);
        $this->setThrowError($throwError);
        $this->setStore($store);
        $this->setPageNumber($pageNumber);
    }

    public function getTargetFormat(): string
    {
        return $this->targetFormat;
    }

    public function setTargetFormat(string $targetFormat): self
    {
        if (!\array_key_exists($targetFormat, \array_flip(self::$formats))) {
            throw new InvalidArgumentException(\sprintf('Format \'%s\' not supported. Supports formats are %s', $targetFormat, \implode(', ', self::$formats)));
        }

        $this->targetFormat = $targetFormat;

        return $this;
    }

    public function throwError(): bool
    {
        return $this->throwError;
    }

    public function setThrowError(bool $throwError): self
    {
        $this->throwError = $throwError;

        return $this;
    }

    public function store(): bool
    {
        return $this->store;
    }

    public function setStore(bool $store): self
    {
        $this->store = $store;

        return $this;
    }

    public function getPageNumber(): ?int
    {
        return $this->pageNumber;
    }

    public function setPageNumber(?int $pageNumber): self
    {
        $this->pageNumber = $pageNumber;

        return $this;
    }

    public function setSaveInGroup(bool $saveInGroup): self
    {
        $this->saveToGroup = $saveInGroup;

        return $this;
    }

    public function isSaveInGroup(): bool
    {
        return $this->pageNumber !== null ? false : $this->saveToGroup;
    }
}
