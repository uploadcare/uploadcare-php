<?php

namespace Uploadcare\AuthUrl\Token;

/**
 * Akamai Token Generator.
 *
 * @see https://uploadcare.com/docs/security/secure_delivery/
 */
class AkamaiToken implements TokenInterface
{
    protected static $algorithms = ['sha256', 'sha1', 'md5'];
    protected static $template = 'https://{cdn}/{uuid}/?token=exp={timestamp}~acl=/{uuid}/~hmac={token}';
    protected static $fieldDelimiter = '~';

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
    private $algo = 'sha256';

    /**
     * @var string|null Access control list
     */
    private $acl = null;

    /**
     * AkamaiToken constructor.
     *
     * @param string $key
     * @param int    $window
     */
    public function __construct($key, $window = 300)
    {
        $this->setKey($key);
        $this->setWindow($window);
    }

    /**
     * @return string
     */
    public function getUrlTemplate()
    {
        return \str_replace('~', self::$fieldDelimiter, self::$template);
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
        $this->window = (int) $window;

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
     * @return string
     */
    public function getAcl()
    {
        if ($this->acl === null) {
            throw new TokenException('You must set file uuid as ACL to generate token');
        }

        return $this->acl;
    }

    /**
     * @param string|null $acl
     *
     * @return AkamaiToken
     */
    public function setAcl($acl)
    {
        $this->acl = $acl;

        return $this;
    }

    /**
     * Token expiration timestamp.
     *
     * @return int
     */
    public function getExpired()
    {
        return \time() + $this->getWindow();
    }

    /**
     * Token string.
     *
     * @return string
     */
    public function getToken()
    {
        $token = $this->makeField('exp', $this->getExpired());
        $token .= $this->makeField('acl', \sprintf('/%s/', $this->getAcl()));

        $tokenDigest = \rtrim($token, self::$fieldDelimiter);

        return \hash_hmac($this->getAlgo(), $tokenDigest, \hex2bin($this->getKey()));
    }

    /**
     * @param string          $fieldName
     * @param string|int|null $value
     *
     * @return string
     */
    private function makeField($fieldName, $value)
    {
        if ($value === null) {
            return '';
        }

        return \sprintf('%s=%s%s', $fieldName, (string) $value, self::$fieldDelimiter);
    }
}
