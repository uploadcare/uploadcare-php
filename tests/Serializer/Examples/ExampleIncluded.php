<?php declare(strict_types=1);

namespace Tests\Serializer\Examples;

use Uploadcare\Interfaces\SerializableInterface;

class ExampleIncluded implements SerializableInterface
{
    private ?\DateTimeInterface $dateTime;

    public static function rules(): array
    {
        return [
            'dateTime' => \DateTime::class,
        ];
    }

    public function setDateTime(\DateTimeInterface $dateTime): self
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    public function getDateTime(): ?\DateTimeInterface
    {
        return $this->dateTime;
    }
}
