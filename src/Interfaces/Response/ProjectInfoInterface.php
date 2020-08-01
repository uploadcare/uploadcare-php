<?php

namespace Uploadcare\Interfaces\Response;

interface ProjectInfoInterface
{
    /**
     * @return array
     */
    public function getCollaborators();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getPubKey();

    /**
     * @return bool
     */
    public function isAutostoreEnabled();
}
