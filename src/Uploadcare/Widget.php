<?php

namespace Uploadcare;

use Uploadcare\Signature\SignatureInterface;

class Widget
{
    /**
     * Uploadcare widget version
     */
    const VERSION = '3.x';

    /**
     * Api instance
     *
     * @var Api
     */
    private $api = null;

    /**
     * @var SignatureInterface|null
     */
    private $secureSignature = null;

    /**
     * Constructor
     *
     * @param Api $api
     * @param SignatureInterface $signature
     */
    public function __construct(Api $api, SignatureInterface $signature = null)
    {
        $this->api = $api;
        $this->secureSignature = $signature;
    }

    /**
     * Return secure signature for signed uploads.
     *
     * @return SignatureInterface|null
     */
    public function getSecureSignature()
    {
        return $this->secureSignature;
    }

    /**
     * Returns <script> sections to include Uploadcare widget
     *
     * @param string $version Uploadcare version
     * @param bool $async
     * @param bool $full
     * @return string
     */
    public function getScriptTag($version = null, $async = false, $full = true)
    {
        $async_attr = $async ? 'async="true"' : '';
        $result = <<<EOT
<script>UPLOADCARE_PUBLIC_KEY = "{$this->api->getPublicKey()}";</script>
<script {$async_attr} src="{$this->getScriptSrc($version, $full)}" charset="UTF-8"></script>
EOT;
        return $result;
    }

    /**
     * Return URL for javascript widget.
     * If no version is provided method will use default(current) version
     *
     * @param string $version Version of Uploadcare.com widget
     * @param bool $full
     * @return string
     */
    public function getScriptSrc($version = null, $full = true)
    {
        if (!$version) {
            $version = self::VERSION;
        }
        if ($full) {
            $tail = "uploadcare.full.min.js";
        } else {
            $tail = "uploadcare.min.js";
        }

        return sprintf($this->api->getCdnUri() . '/libs/widget/%s/'. $tail, $version);
    }

    /**
     * Return data-integration attribute.
     *
     * @return string
     */
    public function getIntegrationData()
    {
        $integrationData = '';

        $framework = $this->api->getFramework();
        if ($framework) {
            $integrationData .= $framework;
        }

        $extension = $this->api->getExtension();
        if ($extension) {
            $integrationData .= '; '.$extension;
        }

        return $integrationData;
    }

    /**
     * Enable signed uploads.
     *
     * @param array $attributes Widget attributes.
     * @return array Array with attributes.
     */
    private function withSignatureAttributes($attributes)
    {
        $signature = $this->secureSignature;
        if ($signature) {
            $attributes = array_merge($attributes, array(
                'data-secure-signature' => $signature->getSignature(),
                'data-secure-expire' => $signature->getExpire(),
            ));
        }

        return $attributes;
    }

    /**
     * Gets input tag to use with widget
     *
     * @param string $name Input name
     * @param array $attributes Custom attributes to include
     * @return string
     */
    public function getInputTag($name, $attributes = array())
    {
        $attributes = $this->withSignatureAttributes($attributes);

        $to_compile = array();
        foreach ($attributes as $key => $value) {
            $to_compile[] = sprintf('%s="%s"', $key, $value);
        }
        return sprintf('<input type="hidden" role="uploadcare-uploader" name="%s" data-upload-url-base="" data-integration="%s" %s />', $name, $this->getIntegrationData(), join(' ', $to_compile));
    }
}
