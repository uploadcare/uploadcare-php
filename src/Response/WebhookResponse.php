<?php declare(strict_types=1);

namespace Uploadcare\Response;

use Uploadcare\Interfaces\Response\WebhookInterface;
use Uploadcare\Interfaces\SerializableInterface;

class WebhookResponse implements WebhookInterface, SerializableInterface
{
    private int $id = 0;
    private ?\DateTimeInterface $created = null;
    private ?\DateTimeInterface $updated;
    private ?string $event = null;
    private ?string $targetUrl = null;
    private int $project = 0;
    private bool $isActive = true;
    private ?string $signingSecret = null;

    public static function rules(): array
    {
        return [
            'id' => 'int',
            'created' => \DateTime::class,
            'updated' => \DateTime::class,
            'event' => 'string',
            'targetUrl' => 'string',
            'project' => 'int',
            'isActive' => 'bool',
            'signingSecret' => 'string',
        ];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(\DateTime $created): self
    {
        $this->created = $created;

        return $this;
    }

    public function getUpdated(): ?\DateTimeInterface
    {
        return $this->updated;
    }

    public function setUpdated(\DateTime $updated): self
    {
        $this->updated = $updated;

        return $this;
    }

    public function getEvent(): ?string
    {
        return $this->event;
    }

    public function setEvent(string $event): self
    {
        $this->event = $event;

        return $this;
    }

    /**
     * @return string
     */
    public function getTargetUrl(): ?string
    {
        return $this->targetUrl;
    }

    public function setTargetUrl(string $targetUrl): self
    {
        $this->targetUrl = $targetUrl;

        return $this;
    }

    public function getProject(): int
    {
        return $this->project;
    }

    public function setProject(int $project): self
    {
        $this->project = $project;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function setSigningSecret(?string $signingSecret): self
    {
        $this->signingSecret = $signingSecret;

        return $this;
    }

    public function getSigningSecret(): ?string
    {
        return $this->signingSecret;
    }
}
