<?php declare(strict_types=1);

namespace Uploadcare\Response;

use Uploadcare\Interfaces\Response\ResponseProblemInterface;
use Uploadcare\Interfaces\SerializableInterface;

final class ResponseProblem implements ResponseProblemInterface, SerializableInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $reason;

    public static function rules(): array
    {
        return [
            'id' => 'string',
            'reason' => 'string',
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getReason(): string
    {
        return $this->reason;
    }

    public function setReason(string $reason): self
    {
        $this->reason = $reason;

        return $this;
    }
}
