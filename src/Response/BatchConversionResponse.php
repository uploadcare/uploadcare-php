<?php declare(strict_types=1);

namespace Uploadcare\Response;

use Uploadcare\Conversion\ConvertedCollection;
use Uploadcare\Conversion\ConvertedItem;
use Uploadcare\Interfaces\File\CollectionInterface;
use Uploadcare\Interfaces\Response\BatchResponseInterface;
use Uploadcare\Interfaces\Response\ResponseProblemInterface;
use Uploadcare\Interfaces\SerializableInterface;

/**
 * Response for conversion request.
 */
final class BatchConversionResponse implements BatchResponseInterface, SerializableInterface
{
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
        $this->result = new ConvertedCollection();
    }

    public static function rules(): array
    {
        return [
            'problems' => 'array',
            'result' => ConvertedCollection::class,
        ];
    }

    public function getStatus(): string
    {
        return 'ok';
    }

    public function getProblems(): array
    {
        return $this->problems;
    }

    public function getResult(): CollectionInterface
    {
        return $this->result;
    }

    public function setProblems(array $problems): self
    {
        foreach ($problems as $k => $problem) {
            $item = null;
            if (\is_string($problem)) {
                $item = (new ResponseProblem())
                    ->setId($k)
                    ->setReason($problem);
            }
            if (\is_array($problem)) {
                foreach ($problem as $sKey => $sValue) {
                    $item = (new ResponseProblem())
                        ->setId($sKey)
                        ->setReason($sValue);
                }
            }

            if ($item instanceof ResponseProblemInterface) {
                $this->addProblem($item);
            }
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

    public function addResult(ConvertedItem $item): self
    {
        if (!$this->result->contains($item)) {
            $this->result->add($item);
        }

        return $this;
    }
}
