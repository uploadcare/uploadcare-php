<?php

namespace Uploadcare\AuthUrl;

class KeyCdnUrlGenerator extends AbstractUrlGenerator
{
    protected static $template = 'https://{cdn}/{uuid}/?token={token}&expire={timestamp}';

    protected function getTemplate()
    {
        return self::$template;
    }
}
