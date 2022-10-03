<?php declare(strict_types=1);

namespace Uploadcare\AuthUrl\Token;

/**
 * Akamai Token Generator.
 *
 * @psalm-suppress PropertyNotSetInConstructor
 *
 * @see https://uploadcare.com/docs/security/secure_delivery/
 */
class AkamaiToken implements TokenInterface
{
    protected static array $algorithms = ['sha256', 'sha1', 'md5'];
    protected static string $template = 'https://{cdn}/{uuid}/?token=exp={timestamp}~acl=/{uuid}/~hmac={token}';
    protected static string $fieldDelimiter = '~';

    private string $key;
    private int $window;

    /**
     * @var string Encryption algorithm
     */
    private string $algo = 'sha256';

    /**
     * @var string|null Access control list
     */
    private ?string $acl = null;

    /**
     * AkamaiToken constructor.
     */
    public function __construct(string $key, int $window = 300)
    {
        $this->setKey($key);
        $this->setWindow($window);
    }

    public function getUrlTemplate(): string
    {
        return \str_replace('~', self::$fieldDelimiter, self::$template);
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function setKey(string $key): self
    {
        if (\preg_match('/^[a-fA-F0-9]+$/', $key) && (\strlen($key) % 2) === 0) {
            $this->key = $key;

            return $this;
        }

        throw new TokenException('Key must be a hex string (a-f,0-9 and even number of chars)');
    }

    public function getWindow(): int
    {
        return $this->window;
    }

    public function setWindow(int $window): self
    {
        if ($window <= 0) {
            throw new TokenException('Window must me a number larger than 0');
        }
        $this->window = $window;

        return $this;
    }

    public function getAlgo(): string
    {
        return $this->algo;
    }

    public function setAlgo(string $algo): self
    {
        if (!\in_array($algo, self::$algorithms, true)) {
            throw new TokenException(\sprintf('Invalid algorithm. Must be one of %s', \implode(', ', self::$algorithms)));
        }

        $this->algo = $algo;

        return $this;
    }

    public function getAcl(): string
    {
        if ($this->acl === null) {
            throw new TokenException('You must set file uuid as ACL to generate token');
        }

        return $this->acl;
    }

    public function setAcl(?string $acl): self
    {
        $this->acl = $acl;

        return $this;
    }

    /**
     * Token expiration timestamp.
     */
    public function getExpired(): int
    {
        return \time() + $this->getWindow();
    }

    /**
     * Token string.
     */
    public function getToken(): string
    {
        $token = $this->makeField('exp', $this->getExpired());
        $token .= $this->makeField('acl', \sprintf('/%s/', $this->getAcl()));

        $tokenDigest = \rtrim($token, self::$fieldDelimiter);

        return \hash_hmac($this->getAlgo(), $tokenDigest, \hex2bin($this->getKey()));
    }

    /**
     * @param string|int|null $value
     */
    private function makeField(string $fieldName, $value): string
    {
        if ($value === null) {
            return '';
        }

        return \sprintf('%s=%s%s', $fieldName, $value, self::$fieldDelimiter);
    }
}
