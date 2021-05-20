<?php declare(strict_types=1);

namespace Uploadcare\Interfaces\Response;

interface ProjectInfoInterface
{
    /**
     * @return array
     */
    public function getCollaborators(): array;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return string
     */
    public function getPubKey(): string;

    /**
     * @return bool
     */
    public function isAutostoreEnabled(): bool;
}
