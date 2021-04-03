<?php declare(strict_types=1);

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

    public static function rules(): array
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
    public function getCollaborators(): array
    {
        return $this->collaborators;
    }

    /**
     * @param array $collaborators
     *
     * @return ProjectInfoResponse
     */
    public function setCollaborators(array $collaborators): self
    {
        $this->collaborators = $collaborators;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return ProjectInfoResponse
     */
    public function setName($name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getPubKey(): string
    {
        return $this->pubKey;
    }

    /**
     * @param string $pubKey
     *
     * @return ProjectInfoResponse
     */
    public function setPubKey(string $pubKey): self
    {
        $this->pubKey = $pubKey;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAutostoreEnabled(): bool
    {
        return $this->autostoreEnabled;
    }

    /**
     * @param bool $autostoreEnabled
     *
     * @return ProjectInfoResponse
     */
    public function setAutostoreEnabled(bool $autostoreEnabled): self
    {
        $this->autostoreEnabled = $autostoreEnabled;

        return $this;
    }
}
