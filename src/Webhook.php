<?php

namespace Uploadcare;

use Uploadcare\Interfaces\Api\WebhookApiInterface;
use Uploadcare\Interfaces\Response\WebhookInterface;
use Uploadcare\Response\WebhookResponse;

class Webhook implements WebhookInterface
{
    /**
     * @var WebhookResponse|WebhookInterface
     */
    private $inner;

    /**
     * @var WebhookApiInterface
     */
    private $api;

    public function __construct(WebhookInterface $inner, WebhookApiInterface $api)
    {
        $this->inner = $inner;
        $this->api = $api;
    }

    public function delete()
    {
        $this->api->deleteWebhook($this->getTargetUrl());
    }

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->inner->getId();
    }

    /**
     * @inheritDoc
     */
    public function getCreated()
    {
        return $this->inner->getCreated();
    }

    /**
     * @inheritDoc
     */
    public function getUpdated()
    {
        return $this->inner->getUpdated();
    }

    /**
     * @inheritDoc
     */
    public function getEvent()
    {
        return $this->inner->getEvent();
    }

    /**
     * @inheritDoc
     */
    public function getTargetUrl()
    {
        return $this->inner->getTargetUrl();
    }

    /**
     * @inheritDoc
     */
    public function getProject()
    {
        return $this->inner->getProject();
    }

    /**
     * @inheritDoc
     */
    public function isActive()
    {
        return $this->inner->isActive();
    }
}
