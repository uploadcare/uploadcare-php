<?php declare(strict_types=1);

namespace Uploadcare\Interfaces\File\AppData;

interface ClamAvDataInterface
{
    public function isInfected(): bool;

    public function getInfectedWith(): ?string;
}
