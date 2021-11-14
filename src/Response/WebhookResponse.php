<?php declare(strict_types=1);

namespace Uploadcare\Response;

use Uploadcare\Interfaces\Response\WebhookInterface;
use Uploadcare\Interfaces\SerializableInterface;

class WebhookResponse implements WebhookInterface, SerializableInterface
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $created;

    /**
     * @var \DateTime
     */
    private $updated;

    /**
     * @var string
     */
    private $event;

    /**
     * @var string
     */
    private $targetUrl;

    /**
     * @var int
     */
    private $project;

    /**
     * @var bool
     */
    private $isActive = true;

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
        ];
    }

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return WebhookResponse
     */
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getCreated(): \DateTimeInterface
    {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     *
     * @return WebhookResponse
     */
    public function setCreated(\DateTime $created): self
    {
        $this->created = $created;

        return $this;
    }

    public function getUpdated(): \DateTimeInterface
    {
        return $this->updated;
    }

    /**
     * @param \DateTime $updated
     *
     * @return WebhookResponse
     */
    public function setUpdated(\DateTime $updated): self
    {
        $this->updated = $updated;

        return $this;
    }

    public function getEvent(): string
    {
        return $this->event;
    }

    /**
     * @param string $event
     *
     * @return WebhookResponse
     */
    public function setEvent(string $event): self
    {
        $this->event = $event;

        return $this;
    }

    /**
     * @return string
     */
    public function getTargetUrl(): string
    {
        return $this->targetUrl;
    }

    /**
     * @param string $targetUrl
     *
     * @return WebhookResponse
     */
    public function setTargetUrl(string $targetUrl): self
    {
        $this->targetUrl = $targetUrl;

        return $this;
    }

    /**
     * @return int
     */
    public function getProject(): int
    {
        return $this->project;
    }

    /**
     * @param int $project
     *
     * @return WebhookResponse
     */
    public function setProject(int $project): self
    {
        $this->project = $project;

        return $this;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->isActive;
    }

    /**
     * @param bool $isActive
     *
     * @return WebhookResponse
     */
    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }
}
