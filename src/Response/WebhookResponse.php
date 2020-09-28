<?php

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

    public static function rules()
    {
        return [
            'id' => 'int',
            'created' => \DateTime::class,
            'updated' => \DateTime::class,
            'event' => 'string',
            'targetUrl' => 'string',
            'project' => 'int',
        ];
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return WebhookResponse
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     *
     * @return WebhookResponse
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param \DateTime $updated
     *
     * @return WebhookResponse
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * @return string
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @param string $event
     *
     * @return WebhookResponse
     */
    public function setEvent($event)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * @return string
     */
    public function getTargetUrl()
    {
        return $this->targetUrl;
    }

    /**
     * @param string $targetUrl
     *
     * @return WebhookResponse
     */
    public function setTargetUrl($targetUrl)
    {
        $this->targetUrl = $targetUrl;

        return $this;
    }

    /**
     * @return int
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @param int $project
     *
     * @return WebhookResponse
     */
    public function setProject($project)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->isActive;
    }

    /**
     * @param bool $isActive
     *
     * @return WebhookResponse
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }
}
