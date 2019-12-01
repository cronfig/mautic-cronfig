<?php
/*
 * @package     Cronfig Mautic Bundle
 * @copyright   2019 Cronfig.io. All rights reserved
 * @author      Jan Linhart
 * @link        http://cronfig.io
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\CronfigBundle\Api;

use GuzzleHttp\Psr7\Request;
use Http\Adapter\Guzzle6\Client;
use Guzzle\Http\Message\RequestInterface;
use MauticPlugin\CronfigBundle\Exception\ApiException;
use MauticPlugin\CronfigBundle\Exception\GraphQlException;
use MauticPlugin\CronfigBundle\Exception\MissingJwtException;

class Connection
{
    /**
     * @var Config
     */
    private $apiConfig;

    /**
     * @var Client
     */
    private $httpClient;

    /**
     * @var QueryBuilder
     */
    private $queryBuilder;

    public function __construct(Config $apiConfig, Client $httpClient, QueryBuilder $queryBuilder)
    {
        $this->apiConfig = $apiConfig;
        $this->httpClient = $httpClient;
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * @param string $query
     * @param array  $variables
     *
     * @return array
     *
     * @throws ApiException|GraphQlException
     */
    public function query(string $query, array $variables = []): array
    {
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Accept-Encoding' => 'gzip, deflate, br',
            'Connection' => 'keep-alive',
            'User-Agent' => 'Jan\'s minimal GraphQL client',
        ];

        // Get the JWT token for all queries except the signIn query.
        if (strpos($query, 'signIn') === false) {
            $headers['x-token'] = $this->getJwtToken();
        }

        $content = json_encode(['query' => $query, 'variables' => $variables]);
        $request = new Request(RequestInterface::POST, $this->apiConfig->getEndpoint(), $headers, $content);
        $response = $this->httpClient->sendRequest($request);

        if ($response->getStatusCode() >= 300) {
            throw new ApiException((string) $response->getBody(), $response->getStatusCode());
        }

        $payload = json_decode((string) $response->getBody(), true);

        if (isset($payload['errors'])) {
            throw new GraphQlException((string) $response->getBody());
        }

        return $payload;
    }

    private function getJwtToken(): string
    {
        try {
            return $this->apiConfig->getJwt();
        } catch (MissingJwtException $e) {
            $response = $this->query(
                $this->queryBuilder->buildJwtTokenQuery(
                    $this->apiConfig->getApiKey()
                )
            );

            $jwt = $response['data']['signIn']['token'];

            $this->apiConfig->setJwt($jwt);

            return $jwt;
        }
    }
}
