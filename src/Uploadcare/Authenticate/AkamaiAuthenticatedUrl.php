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
    private $algo;

    /**
     * @param string $key secret key for authentication token generation
     * @param int $lifetime
     * @param string $algo algorithm, one of 'sha256','sha1','md5'
     * @throws Exception
     */
    public function __construct($key, $lifetime, $algo = 'sha256')
    {
        if ($lifetime > 3600) {
            throw new \Exception('Lifetime of Access tokens can\'t be longer than one hour for akamai CDN provider');
        }

        $now = new DateTime();
        $dateTimeTimestamp = $now->getTimestamp();
        $expire = $dateTimeTimestamp + $lifetime;
        $this->expire = $expire;

        $this->key = $key;

        if (in_array($algo, array('sha256', 'sha1', 'md5'))) {
            $this->algo = $algo;
        } else {
            throw new \Exception("Invalid algorithm, must be one of 'sha256', 'sha1' or 'md5'");
        }
        $this->algo = $algo;
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

        $signature = hash_hmac($this->getAlgo(), rtrim($m_token, $this->getFieldDelimiter()), $this->h2b($this->getKey()));
        return $url . '?token=' . $m_token . 'hmac=' . $signature;
    }

    protected function h2b($str)
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

    public function getAlgo()
    {
        return $this->algo;
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
