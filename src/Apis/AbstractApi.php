<?php

namespace Uploadcare\Apis;

use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Uploadcare\Configuration;
use Uploadcare\Exception\HttpException;
use Uploadcare\Interfaces\ConfigurationInterface;
use Uploadcare\Interfaces\Response\ListResponseInterface;

/**
 * Common methods for API's.
 */
abstract class AbstractApi
{
    /**
     * @var ConfigurationInterface
     */
    protected $configuration;

    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @param ListResponseInterface $response
     *
     * @return array|null
     */
    protected function nextParameters(ListResponseInterface $response)
    {
        if (($next = $response->getNext()) === null) {
            return null;
        }

        $query = \parse_url($next);
        if (!isset($query['query']) || empty($query['query'])) {
            return null;
        }
        $query = (string) $query['query'];
        $parameters = [];
        \parse_str($query, $parameters);

        return $parameters;
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array  $data
     *
     * @return ResponseInterface
     *
     * @throws HttpException
     */
    protected function request($method, $uri, array $data = [])
    {
        $date = \date_create();

        $stringData = '';
        $parameters = [];
        $contentType = 'application/json';

        if (isset($data['form_params'])) {
            $stringData = \http_build_query($data['form_params']);
            $parameters['form_params'] = $data['form_params'];
            unset($data['form_params']);
            $contentType = 'application/x-www-form-urlencoded';
        }
        if (isset($data['body'])) {
            $stringData = $data['body'];
            $parameters['body'] = $data['body'];
            unset($data['body']);
        }

        $uriForSign = $uri;
        if (isset($data['query'])) {
            $uriForSign .= '?' . \http_build_query($data['query']);
        }

        if (isset($data['Content-Type'])) {
            $contentType = $data['Content-Type'];
            unset($data['Content-Type']);
        }

        $headers = $this->configuration->getAuthHeaders($method, $uriForSign, $stringData, $contentType, $date);
        $headers['Accept'] = \sprintf('application/vnd.uploadcare-v%s+json', Configuration::API_VERSION);
        $headers = \array_merge($this->configuration->getHeaders(), $headers);
        if (\strpos($uri, 'http') !== 0) {
            $uri = \sprintf('https://%s/%s', Configuration::API_BASE_URL, $uri);
        }

        $parameters['headers'] = $headers;
        $parameters = \array_merge($data, $parameters);

        try {
            return $this->configuration->getClient()->request($method, $uri, $parameters);
        } catch (GuzzleException $e) {
            throw new HttpException('', 0, ($e instanceof \Exception) ? $e : null);
        }
    }
}
