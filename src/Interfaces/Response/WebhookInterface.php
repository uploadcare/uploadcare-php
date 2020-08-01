<?php

namespace Uploadcare\Interfaces\Response;

interface WebhookInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @return \DateTimeInterface
     */
    public function getCreated();

    /**
     * @return \DateTimeInterface
     */
    public function getUpdated();

    /**
     * @return string
     */
    public function getEvent();

    /**
     * @return string
     */
    public function getTargetUrl();

    /**
     * @return int
     */
    public function getProject();

    /**
     * @return bool
     */
    public function isActive();
}
