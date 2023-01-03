<?php

declare(strict_types=1);

namespace Avency\Gitea\Endpoint\Repositories;

use Avency\Gitea\Client;

/**
 * Repositories Branches Trait
 */
trait BranchesTrait
{
    /**
     * @param string $owner
     * @param string $repositoryName
     * @return array
     */
    public function getBranches(string $owner, string $repositoryName): array
    {
        $response = $this->client->request(self::BASE_URI . '/' . $owner . '/' . $repositoryName . '/branches');

        return \GuzzleHttp\json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @param string $owner
     * @param string $repositoryName
     * @param $branch
     * @return array
     */
    public function getBranche(string $owner, string $repositoryName, string $branch): array
    {
        $response = $this->client->request(self::BASE_URI . '/' . $owner . '/' . $repositoryName . '/branches/' . $branch);

        return \GuzzleHttp\json_decode($response->getBody()->getContents(), true);
    }
    
     /**
     * @param string $owner
     * @param string $repositoryName
     * @param string $baseBranchName
     * @param string $newBranchName
     * @return array
     */
    public function createBranche(string $owner, string $repositoryName, string $baseBranchName, string $newBranchName)
    {
        $options['json'] = [
            'new_branch_name' => $newBranchName,
            'old_branch_name' => $baseBranchName,
        ];
        $response = $this->client->request(self::BASE_URI . '/' . $owner . '/' . $repositoryName . '/branches', 'POST', $options);

        return \GuzzleHttp\json_decode($response->getBody()->getContents(), true);
    }
    
    /**
     * @param string $owner
     * @param string $repositoryName
     * @param array $config
     * @return array
     */
    public function createBrancheProtection(string $owner, string $repositoryName, array $config)
    {
        $options['json'] = $config;
        $response = $this->client->request(self::BASE_URI . '/' . $owner . '/' . $repositoryName . '/branch_protections', 'POST', $options);

        return \GuzzleHttp\json_decode($response->getBody()->getContents(), true);
    }
    
}
