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

class MissingApiKeyException extends ApiException
{
    public function __construct(string $message = 'API key is missing. Go to the Cronfig plugin configuation form and insert it.')
    {
        parent::__construct($message);
    }
}
