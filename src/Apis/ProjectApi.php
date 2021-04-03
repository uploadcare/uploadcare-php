<?php declare(strict_types=1);

namespace Uploadcare\Apis;

use Uploadcare\Interfaces\Api\ProjectApiInterface;
use Uploadcare\Interfaces\Response\ProjectInfoInterface;
use Uploadcare\Response\ProjectInfoResponse;

class ProjectApi extends AbstractApi implements ProjectApiInterface
{
    /**
     * @return ProjectInfoInterface
     */
    public function getProjectInfo(): ProjectInfoInterface
    {
        $response = $this->request('GET', 'project/');
        $result = $this->configuration->getSerializer()->deserialize($response->getBody()->getContents(), ProjectInfoResponse::class);

        if (!$result instanceof ProjectInfoInterface) {
            throw new \RuntimeException('Unable to deserialize response. Call to support');
        }

        return $result;
    }
}
