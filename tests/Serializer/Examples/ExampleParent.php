<?php declare(strict_types=1);

namespace Tests\Serializer\Examples;

use Uploadcare\Interfaces\SerializableInterface;

class ExampleParent implements SerializableInterface
{
    private ?string $name = null;

    /**
     * @var array|ExampleIncluded[]
     */
    private array $dates = [];

    public static function rules(): array
    {
        return [
            'name' => 'string',
            'dates' => [ExampleIncluded::class],
        ];
    }

    /**
     * @return $this
     */
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function addDate(ExampleIncluded $included): self
    {
        $this->dates[] = $included;

        return $this;
    }

    public function getDates(): array
    {
        return $this->dates;
    }
}
