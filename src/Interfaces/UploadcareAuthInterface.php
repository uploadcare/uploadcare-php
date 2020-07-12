<?php

namespace Uploadcare\Interfaces;

/**
 * UploadcareAuth.
 *
 * @see https://uploadcare.com/docs/rest_api/requests_auth/?utm_source=api-ref&utm_campaign=rest-auth#example-request-uploadcare
 */
interface UploadcareAuthInterface
{
    const AUTH_ALGORITHM = 'sha1';
    const HEADER_DATE_FORMAT = 'D, d M Y H:i:s T';

    /**
     * Formatted date for `Date` header.
     *
     * @param \DateTimeInterface|null $date
     *
     * @return string
     */
    public function getDateHeaderString($date = null);

    /**
     * Auth header.
     *
     * @param string                  $method
     * @param string                  $uri
     * @param string                  $data
     * @param string                  $contentType
     * @param \DateTimeInterface|null $date
     *
     * @return string
     */
    public function getAuthHeaderString($method, $uri, $data, $contentType = 'application/json', $date = null);
}
