<?php

namespace Uploadcare\Apis;

use GuzzleHttp\Exception\GuzzleException;
use function GuzzleHttp\Psr7\stream_for;
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
    const API_VERSION = '0.5';

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
        if (isset($data['body'])) {
            $stringData = \json_encode($data['body']);
            $parameters['body'] = stream_for($data['body']);
            unset($data['body']);
        }
        if (isset($data['form_params'])) {
            $stringData = \json_encode($data['form_params']);
            $parameters['form_params'] = $data['form_params'];
            unset($data['form_params']);
        }
        $uriForSign = $uri;
        if (isset($data['query'])) {
            $uriForSign .= '?' . \http_build_query($data['query']);
        }

        $headers = $this->configuration->getAuthHeaders($method, $uriForSign, $stringData, 'application/json', $date);
        $headers['Accept'] = \sprintf('application/vnd.uploadcare-v%s+json', self::API_VERSION);
        $headers = \array_merge($this->configuration->getHeaders(), $headers);
        if (\strpos('http', $uri) !== 0) {
            $uri = \sprintf('https://%s/%s', Configuration::API_BASE_URL, $uri);
        }

        $parameters = [
            'headers' => $headers,
        ];
        $parameters = \array_merge($data, $parameters);

        try {
            return $this->configuration->getClient()->request($method, $uri, $parameters);
        } catch (GuzzleException $e) {
            throw new HttpException('', 0, ($e instanceof \Exception) ? $e : null);
        }
    }
}
