<?php declare(strict_types=1);

namespace Uploadcare\File\AppData;

use Uploadcare\Interfaces\File\AppData\ClamAvDataInterface;
use Uploadcare\Interfaces\SerializableInterface;

class ClamAvData implements ClamAvDataInterface, SerializableInterface
{
    private bool $infected = false;
    private ?string $infectedWith = null;

    public static function rules(): array
    {
        return [
            'infected' => 'boolean',
            'infectedWith' => 'string',
        ];
    }

    public function isInfected(): bool
    {
        return $this->infected;
    }

    public function setInfected(bool $infected): self
    {
        $this->infected = $infected;

        return $this;
    }

    public function getInfectedWith(): ?string
    {
        return $this->infectedWith;
    }

    public function setInfectedWith(?string $infectedWith): self
    {
        $this->infectedWith = $infectedWith;

        return $this;
    }
}
