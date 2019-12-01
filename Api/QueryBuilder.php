<?php
/*
 * @package     Cronfig Mautic Bundle
 * @copyright   2019 Cronfig.io. All rights reserved
 * @author      Jan Linhart
 * @link        http://cronfig.io
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\CronfigBundle\Api;

class QueryBuilder
{
    public function buildJwtTokenQuery(string $apiKey): string
    {
        return <<<GRAPHQL
mutation {
    signIn (apiKey: "{$apiKey}") {
        token
    }
}
GRAPHQL;
    }

    public function buildGetTasksQuery(int $offset = 0, int $limit = 100): string
    {
        return <<<GRAPHQL
{
    me {
        tasks(offset: {$offset} limit: {$limit}) {
            pageInfo {
                total
            }
            list {
                id
                url
                status
                platform
                period
                timeout
                createdAt
                updatedAt
                triggeredAt
                totalJobCount
                totalErrorCount
                errorCount
            }
        }
    }
}
GRAPHQL;
    }
}
