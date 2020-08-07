<?php

namespace Uploadcare\AuthUrl;

class AkamaiUrlGenerator extends AbstractUrlGenerator
{
    protected static $template = 'https://{cdn}/{uuid}/?token=exp={timestamp}~acl=/{uuid}/~hmac={token}';

    protected function getTemplate()
    {
        return self::$template;
    }
}
