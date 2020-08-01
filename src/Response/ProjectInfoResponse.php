<?php

namespace Uploadcare\Response;

use Uploadcare\Interfaces\Response\ProjectInfoInterface;
use Uploadcare\Interfaces\SerializableInterface;

final class ProjectInfoResponse implements ProjectInfoInterface, SerializableInterface
{
    /**
     * @var array
     */
    private $collaborators;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $pubKey;

    /**
     * @var bool
     */
    private $autostoreEnabled = true;

    public static function rules()
    {
        return [
            'collaborators' => 'array',
            'name' => 'string',
            'pubKey' => 'string',
            'autostoreEnabled' => 'bool',
        ];
    }

    /**
     * @return array
     */
    public function getCollaborators()
    {
        return $this->collaborators;
    }

    /**
     * @param array $collaborators
     *
     * @return ProjectInfoResponse
     */
    public function setCollaborators(array $collaborators)
    {
        $this->collaborators = $collaborators;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return ProjectInfoResponse
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getPubKey()
    {
        return $this->pubKey;
    }

    /**
     * @param string $pubKey
     *
     * @return ProjectInfoResponse
     */
    public function setPubKey($pubKey)
    {
        $this->pubKey = $pubKey;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAutostoreEnabled()
    {
        return $this->autostoreEnabled;
    }

    /**
     * @param bool $autostoreEnabled
     *
     * @return ProjectInfoResponse
     */
    public function setAutostoreEnabled($autostoreEnabled)
    {
        $this->autostoreEnabled = $autostoreEnabled;

        return $this;
    }
}
