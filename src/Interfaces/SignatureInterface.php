<?php declare(strict_types=1);

namespace Uploadcare\Interfaces;

/**
 * Signature for upload requests.
 */
interface SignatureInterface extends UploadcareAuthInterface
{
    public const SIGN_ALGORITHM = 'sha256';
    public const MAX_TTL = 3600;

    /**
     * @return string
     */
    public function getSignature(): string;

    /**
     * @return \DateTimeInterface
     */
    public function getExpire(): \DateTimeInterface;
}
