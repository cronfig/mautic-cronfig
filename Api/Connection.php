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
use MauticPlugin\CronfigBundle\Exception\MissingJwtException;
use Psr\Log\LoggerInterface;

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

    private $logger;

    public function __construct(
        Config $apiConfig,
        Client $httpClient,
        QueryBuilder $queryBuilder,
        LoggerInterface $logger
    ) {
        $this->apiConfig = $apiConfig;
        $this->httpClient = $httpClient;
        $this->queryBuilder = $queryBuilder;
        $this->logger = $logger;
    }

    /**
     * @param string $query
     * @param array  $variables
     *
     * @return array
     *
     * @throws ApiException
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
        if (false === strpos($query, 'signIn')) {
            $headers['x-token'] = $this->getJwtToken();
        }

        $content = json_encode(['query' => $query, 'variables' => $variables]);

        $this->logger->debug('About to query the Cronfig API', ['content' => $content]);

        $request = new Request(RequestInterface::POST, $this->apiConfig->getEndpoint(), $headers, $content);
        $response = $this->httpClient->sendRequest($request);
        $body = (string) $response->getBody();

        if ($response->getStatusCode() >= 300) {
            throw new ApiException($body, $response->getStatusCode());
        }

        $payload = json_decode($body, true);

        $this->logger->debug('Successful Cronfig API response', ['payload' => $payload]);

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
