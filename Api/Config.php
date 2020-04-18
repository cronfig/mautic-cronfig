<?php
/*
 * @package     Cronfig Mautic Bundle
 * @copyright   2019 Cronfig.io. All rights reserved
 * @author      Jan Linhart
 * @link        http://cronfig.io
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\CronfigBundle\Api;

use Mautic\CoreBundle\Helper\CacheStorageHelper;
use MauticPlugin\CronfigBundle\Exception\MissingApiKeyException;
use MauticPlugin\CronfigBundle\Exception\MissingJwtException;

final class Config
{
    private const CACHE_TOKEN_API_KEY = 'cronfig_api_token';
    private const CACHE_TOKEN_JWT_KEY = 'cronfig_jwt_token';

    /**
     * @var CacheStorageHelper
     */
    private $cacheStorageHelper;

    public function __construct(CacheStorageHelper $cacheStorageHelper)
    {
        $this->cacheStorageHelper = $cacheStorageHelper;
    }

    public function getEndpoint(): string
    {
        return 'http://127.0.0.1:3000/graphql'; // @todo change for production or make configurable.
    }

    /**
     * @throws MissingApiKeyException
     */
    public function getApiKey(): string
    {
        $apiKey = $this->cacheStorageHelper->get(self::CACHE_TOKEN_API_KEY);

        if (!$apiKey) {
            throw new MissingApiKeyException();
        }

        return $apiKey;
    }

    public function setApiKey(string $apiKey): void
    {
        $this->cacheStorageHelper->set(self::CACHE_TOKEN_API_KEY, $apiKey);
    }

    /**
     * @throws MissingJwtException
     */
    public function getJwt(): ?string
    {
        $jwt = $this->cacheStorageHelper->get(self::CACHE_TOKEN_JWT_KEY);

        if (!$jwt) {
            throw new MissingJwtException();
        }

        return $jwt;
    }

    public function setJwt(string $apiKey): void
    {
        $this->cacheStorageHelper->set(self::CACHE_TOKEN_JWT_KEY, $apiKey, 3600);
    }
}
