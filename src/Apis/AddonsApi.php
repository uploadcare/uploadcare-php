<?php declare(strict_types=1);

namespace Uploadcare\Apis;

use Uploadcare\Interfaces\Api\AddonsApiInterface;

class AddonsApi extends AbstractApi implements AddonsApiInterface
{
    public function requestAwsRecognition($id): string
    {
        $response = $this->request('POST', '/addons/aws_rekognition_detect_labels/execute/', [
            'body' => ['target' => (string) $id],
        ])->getBody()->getContents();

        return $this->getResponseParameter($response, 'request_id');
    }

    public function checkAwsRecognition(string $id): string
    {
        $uri = \sprintf('/addons/aws_rekognition_detect_labels/execute/status/?request_id=%s', $id);
        $response = $this->request('GET', $uri)->getBody()->getContents();

        return $this->getResponseParameter($response, 'status');
    }

    public function requestAntivirusScan($id, bool $purge = null): string
    {
        $parameters = ['target' => (string) $id];
        if (\is_bool($purge)) {
            $parameters['params']['purge_infected'] = $purge;
        }

        $response = $this->request('POST', '/addons/uc_clamav_virus_scan/execute/', [
            'body' => $parameters,
        ])->getBody()->getContents();

        return $this->getResponseParameter($response, 'request_id');
    }

    public function checkAntivirusScan(string $id): string
    {
        $uri = \sprintf('/addons/uc_clamav_virus_scan/execute/status/?request_id=%s', $id);
        $response = $this->request('GET', $uri)->getBody()->getContents();

        return $this->getResponseParameter($response, 'status');
    }

    private function getResponseParameter(string $content, string $parameter): string
    {
        try {
            $resultArray = \json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable $e) {
            throw new \RuntimeException($e->getMessage());
        }

        $result = $resultArray[$parameter] ?? null;
        if (!\is_string($result)) {
            throw new \RuntimeException('Wrong response. Call to support');
        }

        return $result;
    }
}
