<?php
/*
 * @package     Cronfig Mautic Bundle
 * @copyright   2019 Cronfig.io. All rights reserved
 * @author      Jan Linhart
 * @link        http://cronfig.io
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\CronfigBundle\Provider;

use Doctrine\DBAL\Connection;
use Mautic\CoreBundle\Helper\CoreParametersHelper;

class TaskStatusProvider
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var CoreParametersHelper
     */
    private $coreParametersHelper;

    /**
     * Cached result of the query.
     *
     * @var bool|null
     */
    private $segmentsAreActive;

    /**
     * Cached result of the query.
     *
     * @var bool|null
     */
    private $campaignsAreActive;

    public function __construct(
        Connection $connection,
        CoreParametersHelper $coreParametersHelper
    ) {
        $this->connection = $connection;
        $this->coreParametersHelper = $coreParametersHelper;
    }

    public function segmentsAreActive(): bool
    {
        if (null === $this->segmentsAreActive) {
            $queryBuilder = $this->connection->createQueryBuilder();
            $queryBuilder->select('COUNT(*)');
            $queryBuilder->from("{$this->coreParametersHelper->getParameter('db_table_prefix')}lead_lists");
            $queryBuilder->where('is_published = 1');
            $queryBuilder->andWhere('is_published != "a:0:{}"');

            $this->segmentsAreActive = (bool) $queryBuilder->execute()->fetchColumn();
        }

        return $this->segmentsAreActive;
    }

    public function campaignsAreActive(): bool
    {
        if (null === $this->campaignsAreActive) {
            $queryBuilder = $this->connection->createQueryBuilder();
            $queryBuilder->select('COUNT(*)');
            $queryBuilder->from("{$this->coreParametersHelper->getParameter('db_table_prefix')}campaigns");
            $queryBuilder->where('is_published = 1');

            $this->campaignsAreActive = (bool) $queryBuilder->execute()->fetchColumn();
        }

        return $this->campaignsAreActive;
    }

    public function ipLookupDownloadShouldBeActive(): bool
    {
        return true;
    }
}
