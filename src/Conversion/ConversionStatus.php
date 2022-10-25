<?php declare(strict_types=1);

namespace Uploadcare\Conversion;

use Uploadcare\Interfaces\Conversion\{ConversionStatusInterface, StatusResultInterface};
use Uploadcare\Interfaces\SerializableInterface;

class ConversionStatus implements ConversionStatusInterface, SerializableInterface
{
    private string $status = 'pending';
    private ?string $error = null;
    private ?StatusResultInterface $result = null;

    public static function rules(): array
    {
        return [
            'status' => 'string',
            'error' => 'string',
            'result' => ConversionResult::class,
        ];
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function setError(?string $error): self
    {
        $this->error = $error;

        return $this;
    }

    public function getResult(): ?StatusResultInterface
    {
        return $this->result;
    }

    public function setResult(StatusResultInterface $result): self
    {
        $this->result = $result;

        return $this;
    }
}
