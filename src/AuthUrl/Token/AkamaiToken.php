<?php

namespace Uploadcare\AuthUrl\Token;

/**
 * Akamai Token Generator.
 *
 * @see https://learn.akamai.com/en-us/webhelp/download-delivery/download-delivery-implementation-guide/GUID-EB3329D1-C7C5-4F23-9B69-1B1FBFEBF436.html
 */
class AkamaiToken implements TokenInterface
{
    protected static $algorithms = ['sha256', 'sha1', 'md5'];

    /**
     * @var string Encryption key
     */
    private $key;

    /**
     * @var int token lifetime
     */
    private $window;

    /**
     * @var string Encryption algorithm
     */
    private $algo = 'SHA256';

    /**
     * @var string|null Restrict the token to a specific IP address
     */
    private $ip = null;

    private $startTime = 0;

    /**
     * @var string|null Access control list
     */
    private $acl = null;

    /**
     * @var string|null Restrict the token to a specific URL
     */
    private $url = null;

    /**
     * @var string Restrict the token to a specific session ID
     */
    private $sessionId;

    /**
     * @var string|null Additional data
     */
    private $data = null;

    /**
     * @var string|null Salt
     */
    private $salt = null;

    /**
     * @var string Field delimiter
     */
    private $fieldDelimiter = '~';

    /**
     * @var bool
     */
    private $earlyUrlEncoding = false;

    /**
     * AkamaiToken constructor.
     *
     * @param string $key
     * @param int    $window
     */
    public function __construct($key, $window = 300)
    {
        $this->key = $key;
        $this->window = $window;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key
     *
     * @return AkamaiToken
     */
    public function setKey($key)
    {
        if (preg_match('/^[a-fA-F0-9]+$/', $key) && (\strlen($key) % 2) === 0) {
            $this->key = $key;

            return $this;
        }

        throw new TokenException('Key must be a hex string (a-f,0-9 and even number of chars)');
    }

    /**
     * @return int
     */
    public function getWindow()
    {
        return $this->window;
    }

    /**
     * @param int $window
     *
     * @return AkamaiToken
     */
    public function setWindow($window)
    {
        if (!\is_numeric($window) || (int) $window <= 0) {
            throw new TokenException('Window must me a number larger than 0');
        }
        $this->window = $window;

        return $this;
    }

    /**
     * @return string
     */
    public function getAlgo()
    {
        return $this->algo;
    }

    /**
     * @param string $algo
     *
     * @return AkamaiToken
     */
    public function setAlgo($algo)
    {
        if (!\in_array($algo, self::$algorithms)) {
            throw new TokenException(\sprintf('Invalid algorithm. Must be one of %s', \implode(', ', self::$algorithms)));
        }

        $this->algo = $algo;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param string|null $ip
     *
     * @return AkamaiToken
     */
    public function setIp($ip)
    {
        if ($ip === null) {
            return $this;
        }

        $this->validateIp($ip);
        $this->ip = $ip;

        return $this;
    }

    /**
     * @return int
     */
    public function getStartTime()
    {
        if ($this->startTime === 0) {
            return \time();
        }
        if (\is_numeric($this->startTime) && $this->startTime > 0) {
            return (int) $this->startTime;
        }

        throw new TokenException('Start time input invalid or out of range');
    }

    /**
     * @param int $startTime
     *
     * @return AkamaiToken
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAcl()
    {
        return $this->acl;
    }

    /**
     * @param string|null $acl
     *
     * @return AkamaiToken
     */
    public function setAcl($acl)
    {
        if ($this->url !== null) {
            throw new TokenException('Cannot set both an ACL and a URL at the same time');
        }

        $this->acl = $acl;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string|null $url
     *
     * @return AkamaiToken
     */
    public function setUrl($url)
    {
        if ($this->acl !== null) {
            throw new TokenException('Cannot set both an ACL and a URL at the same time');
        }
        $this->url = $url;

        return $this;
    }

    /**
     * @return string
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * @param string $sessionId
     *
     * @return AkamaiToken
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;

        return $this;
    }

    /**
     * @return mixed|null
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param string|null $data
     *
     * @return AkamaiToken
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @param string|null $salt
     *
     * @return AkamaiToken
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * @return string
     */
    public function getFieldDelimiter()
    {
        return $this->fieldDelimiter;
    }

    /**
     * @param string $fieldDelimiter
     *
     * @return AkamaiToken
     */
    public function setFieldDelimiter($fieldDelimiter)
    {
        $this->fieldDelimiter = $fieldDelimiter;

        return $this;
    }

    /**
     * @return bool
     */
    public function isEarlyUrlEncoding()
    {
        return $this->earlyUrlEncoding;
    }

    /**
     * @param bool $earlyUrlEncoding
     *
     * @return AkamaiToken
     */
    public function setEarlyUrlEncoding($earlyUrlEncoding)
    {
        $this->earlyUrlEncoding = $earlyUrlEncoding;

        return $this;
    }

    /**
     * Token string.
     *
     * @return string
     */
    public function getToken()
    {
        $token = $this->makeField('ip', $this->getIp());
        $token .= $this->makeField('st', $this->getStartTime());
        $token .= $this->makeField('exp', $this->getExpired());
        $token .= $this->makeField('acl', $this->getAcl());
        $token .= $this->makeField('id', $this->getSessionId());
        $token .= $this->makeField('data', $this->getData());

        $tokenDigest = $token
            . $this->makeField('url', $this->getUrl())
            . $this->makeField('salt', $this->getSalt());

        $signature = \hash_hmac($this->getAlgo(), \rtrim($tokenDigest, $this->getFieldDelimiter()), $this->hex2bin($this->getKey()));

        return $signature;
    }

    /**
     * Token expiration timestamp.
     *
     * @return int
     */
    public function getExpired()
    {
        return $this->getStartTime() + $this->getWindow();
    }

    /**
     * @param string          $fieldName
     * @param string|int|null $value
     */
    private function makeField($fieldName, $value)
    {
        if ($value === null) {
            return '';
        }

        return \sprintf('%s=%s%s', $fieldName, (string) $value, $this->getFieldDelimiter());
    }

    /**
     * @param string $str
     *
     * @return string
     */
    private function hex2bin($str)
    {
        $bin = '';
        $i = 0;
        while ($i < \strlen($str)) {
            $bin .= \chr(\hexdec($str[$i] . $str[($i + 1)]));
            $i += 2;
        }

        return $bin;
    }

    /**
     * @param mixed $ip
     */
    private function validateIp($ip)
    {
        if (!\is_string($ip)) {
            throw new TokenException(\sprintf('IP must be a string, %s given', (\is_object($ip) ? \get_class($ip) : \gettype($ip))));
        }

        $regex4 = '/^((\d|[1-9]\d|1\d{...}|2[0-4]\d|25[0-5])\\.){3}(\d|[1-9]\d|1\d{...}|2[0-4]\d|25[0-5])$/';
        $regex6 = '/^((([0-9a-fA-F]){1,4})\\:){7}([0-9a-fA-F]){1,4}$/';

        if (\preg_match($regex4, $ip) === false && preg_match($regex6, $ip) === false) {
            throw new TokenException(\sprintf('Given IP \'%s\' is neither IPv4, nor IPv6', $ip));
        }
    }
}
