<?php
/**
 * @package     Cronfig Mautic Bundle
 * @copyright   2016 Cronfig.io. All rights reserved
 * @author      Jan Linhart
 * @link        http://cronfig.io
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\CronfigBundle\Controller;

use Mautic\CoreBundle\Controller\CommonController;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Util\Codes;

/**
 * Class PublicController
 */

class PublicController extends CommonController
{
    /*
     * @param string $command
     */
    public function triggerAction($command)
    {
        $response  = new Response();
        $response->headers->set('Content-Type', 'text/plain');
        $secretKey = $this->request->query->get('secret_key');
        $config    = $this->factory->getParameter('cronfig');

        if (isset($config['secret_key']) && $config['secret_key'] !== $secretKey) {
            $response->setStatusCode(Codes::HTTP_FORBIDDEN);
            $output = 'error: access forbidden';
        } else {
            $command = explode(' ', urldecode($command));
            $errorCount = $this->request->get('error_count', 0);
            $args = array_merge(array('console'), $command);

            if ($errorCount > 2) {
                // Try to force the command if it failed 2 times before
                $args[] = '--force';
            }

            try {
                $input  = new ArgvInput($args);
                $output = new BufferedOutput();
                $app    = new Application($this->get('kernel'));
                $app->setAutoExit(false);
                $result = $app->run($input, $output);
                $output = $output->fetch();
            } catch (\Exception $exception) {
                $output = $exception->getMessage();
                $response->setStatusCode(500);
            }

            // Guess that the output is an error message
            $errorWords = array('--force', 'exception');

            foreach ($errorWords as $errorWord) {
                if (strpos($output, $errorWord) !== false) {
                    $response->setStatusCode(500);
                }
            }
        }

        $response->setContent($output);

        return $response;
    }
}
