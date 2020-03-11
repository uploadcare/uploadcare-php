<?php

namespace Uploadcare\Authenticate;

use Exception;
use DateTime;

/**
 * Class AkamaiAuthenticatedUrl
 * @package Authenticate\AuthenticatedUrl
 */
class AkamaiAuthenticatedUrl implements AuthenticatedUrlInterface
{
    protected $fieldDelimiter = '~';

    /**
     * @var string CDN access key
     */
    private $key;

    /**
     * @var string timestamp of token expiration
     */
    private $expire;

    /**
     * @var string algorithm, one of 'sha256','sha1','md5'
     */
    private static $algo = 'sha256';

    /**
     * @param string $key secret key for authentication token generation
     * @param int $lifetime
     * @throws Exception
     */
    public function __construct($key, $lifetime)
    {
        if ($lifetime > 3600) {
            throw new \Exception('Lifetime of Access tokens can\'t be longer than one hour for akamai CDN provider');
        }

        $now = new DateTime();
        $dateTimeTimestamp = $now->getTimestamp();
        $expire = $dateTimeTimestamp + $lifetime;
        $this->expire = $expire;

        $this->key = $key;
    }

    /**
     * @param string $url
     *
     * @return string
     */
    public function getAuthenticatedUrl($url)
    {
        $m_token = $this->getExprField();
        $m_token .= $this->getAclField($url);

        $signature = hash_hmac(self::$algo, rtrim($m_token, $this->getFieldDelimiter()), $this->h2b($this->getKey()));
        return $url . '?token=' . $m_token . 'hmac=' . $signature;
    }

    protected static function h2b($str)
    {
        $bin = "";
        $i = 0;
        do {
            $bin .= chr(hexdec($str{$i} . $str{($i + 1)}));
            $i += 2;
        } while ($i < strlen($str));
        return $bin;
    }

    public function getExprField()
    {
        return 'exp=' . $this->expire . $this->fieldDelimiter;
    }

    /**
     * @param $url string
     *
     * @return string
     */
    public function getAclField($url)
    {
        return 'acl=' . $url . $this->fieldDelimiter;
    }

    public function getFieldDelimiter()
    {
        return $this->fieldDelimiter;
    }

    public function getKey()
    {
        return $this->key;
    }
}
