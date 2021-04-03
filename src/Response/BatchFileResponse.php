<?php declare(strict_types=1);

namespace Uploadcare\Response;

use Uploadcare\File\FileCollection;
use Uploadcare\Interfaces\File\CollectionInterface;
use Uploadcare\Interfaces\File\FileInfoInterface;
use Uploadcare\Interfaces\Response\BatchResponseInterface;
use Uploadcare\Interfaces\Response\ResponseProblemInterface;
use Uploadcare\Interfaces\SerializableInterface;

final class BatchFileResponse implements BatchResponseInterface, SerializableInterface
{
    /**
     * @var string
     */
    private $status;

    /**
     * @var ResponseProblemInterface[]
     */
    private $problems;

    /**
     * @var CollectionInterface
     */
    private $result;

    public function __construct()
    {
        $this->problems = [];
        $this->result = new FileCollection();
    }

    public static function rules(): array
    {
        return [
            'status' => 'string',
            'problems' => 'array',
            'result' => FileCollection::class,
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getProblems(): array
    {
        return $this->problems;
    }

    /**
     * @param array $problems
     *
     * @return $this
     */
    public function setProblems(array $problems): self
    {
        foreach ($problems as $key => $value) {
            $item = (new ResponseProblem())
                ->setId($key)
                ->setReason($value);
            $this->addProblem($item);
        }

        return $this;
    }

    public function addProblem(ResponseProblemInterface $problem): self
    {
        if (!\in_array($problem, $this->problems, true)) {
            $this->problems[] = $problem;
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getResult(): CollectionInterface
    {
        return $this->result;
    }

    /**
     * @param CollectionInterface $fileCollection
     *
     * @return self
     */
    public function setResult(CollectionInterface $fileCollection): self
    {
        $this->result = $fileCollection;

        return $this;
    }

    public function addResult(FileInfoInterface $fileInfo): self
    {
        if (!$this->result->contains($fileInfo)) {
            $this->result->add($fileInfo);
        }

        return $this;
    }
}
