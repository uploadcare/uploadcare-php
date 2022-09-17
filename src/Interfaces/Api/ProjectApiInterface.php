<?php declare(strict_types=1);

namespace Uploadcare\Interfaces\Api;

use Uploadcare\Interfaces\Response\ProjectInfoInterface;

interface ProjectApiInterface
{
    /**
     * Getting info about account project.
     */
    public function getProjectInfo(): ProjectInfoInterface;
}
