<?php declare(strict_types=1);

namespace Uploadcare\Interfaces\File\AppData;

interface ClamAvVirusScanInterface
{
    public function getVersion(): ?string;

    public function getDatetimeCreated(): ?\DateTimeInterface;

    public function getDatetimeUpdated(): ?\DateTimeInterface;

    public function getData(): ?ClamAvDataInterface;
}
