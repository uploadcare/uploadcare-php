<?php declare(strict_types=1);

namespace Uploadcare\Interfaces;

/**
 * UploadcareAuth.
 *
 * @see https://uploadcare.com/docs/rest_api/requests_auth/?utm_source=api-ref&utm_campaign=rest-auth#example-request-uploadcare
 */
interface UploadcareAuthInterface
{
    public const AUTH_ALGORITHM = 'sha1';
    public const HEADER_DATE_FORMAT = 'D, d M Y H:i:s T';

    /**
     * Formatted date for `Date` header.
     */
    public function getDateHeaderString(?\DateTimeInterface $date = null): string;

    /**
     * Auth header.
     */
    public function getAuthHeaderString(string $method, string $uri, string $data, string $contentType = 'application/json', ?\DateTimeInterface $date = null): string;
}
