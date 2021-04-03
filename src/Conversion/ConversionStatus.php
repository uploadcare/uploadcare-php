<?php declare(strict_types=1);

namespace Uploadcare\Conversion;

use Uploadcare\Interfaces\Conversion\ConversionStatusInterface;
use Uploadcare\Interfaces\Conversion\StatusResultInterface;
use Uploadcare\Interfaces\SerializableInterface;

class ConversionStatus implements ConversionStatusInterface, SerializableInterface
{
    /**
     * @var string
     */
    private $status;

    /**
     * @var string|null
     */
    private $error;

    /**
     * @var StatusResultInterface
     */
    private $result;

    public static function rules(): array
    {
        return [
            'status' => 'string',
            'error' => 'string',
            'result' => ConversionResult::class,
        ];
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     *
     * @return ConversionStatus
     */
    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getError(): ?string
    {
        return $this->error;
    }

    /**
     * @param string|null $error
     *
     * @return ConversionStatus
     */
    public function setError(?string $error): self
    {
        $this->error = $error;

        return $this;
    }

    public function getResult(): StatusResultInterface
    {
        return $this->result;
    }

    /**
     * @param StatusResultInterface $result
     *
     * @return ConversionStatus
     */
    public function setResult(StatusResultInterface $result): self
    {
        $this->result = $result;

        return $this;
    }
}
