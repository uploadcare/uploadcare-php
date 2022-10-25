<?php declare(strict_types=1);

namespace Uploadcare\Response;

use Uploadcare\File\FileCollection;
use Uploadcare\Interfaces\File\{CollectionInterface, FileInfoInterface};
use Uploadcare\Interfaces\Response\{BatchResponseInterface, ResponseProblemInterface};
use Uploadcare\Interfaces\SerializableInterface;

final class BatchFileResponse implements BatchResponseInterface, SerializableInterface
{
    private string $status = 'ok';

    /**
     * @var ResponseProblemInterface[]
     */
    private array $problems;

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
