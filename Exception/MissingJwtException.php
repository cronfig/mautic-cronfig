<?php

/*
 * @copyright   2019 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\CronfigBundle\Exception;

final class MissingJwtException extends ApiException
{
    public function __construct(string $message = 'JWT is missing. Reauthenticate.')
    {
        parent::__construct($message);
    }
}
