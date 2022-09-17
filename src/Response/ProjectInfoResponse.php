<?php declare(strict_types=1);

namespace Uploadcare\Response;

use Uploadcare\Interfaces\Response\ProjectInfoInterface;
use Uploadcare\Interfaces\SerializableInterface;

final class ProjectInfoResponse implements ProjectInfoInterface, SerializableInterface
{
    private array $collaborators = [];
    private ?string $name = null;
    private ?string $pubKey = null;
    private bool $autostoreEnabled = true;

    public static function rules(): array
    {
        return [
            'collaborators' => 'array',
            'name' => 'string',
            'pubKey' => 'string',
            'autostoreEnabled' => 'bool',
        ];
    }

    public function getCollaborators(): array
    {
        return $this->collaborators;
    }

    public function setCollaborators(array $collaborators): self
    {
        $this->collaborators = $collaborators;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPubKey(): ?string
    {
        return $this->pubKey;
    }

    public function setPubKey(string $pubKey): self
    {
        $this->pubKey = $pubKey;

        return $this;
    }

    public function isAutostoreEnabled(): bool
    {
        return $this->autostoreEnabled;
    }

    public function setAutostoreEnabled(bool $autostoreEnabled): self
    {
        $this->autostoreEnabled = $autostoreEnabled;

        return $this;
    }
}
