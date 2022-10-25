<?php declare(strict_types=1);

namespace Uploadcare\Interfaces\Response;

interface ProjectInfoInterface
{
    public function getCollaborators(): array;

    public function getName(): ?string;

    public function getPubKey(): ?string;

    public function isAutostoreEnabled(): bool;
}
