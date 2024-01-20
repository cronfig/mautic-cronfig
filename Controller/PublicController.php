<?php

declare(strict_types=1);

namespace MauticPlugin\CronfigBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Mautic\CoreBundle\Controller\CommonController;
use Mautic\CoreBundle\Helper\CoreParametersHelper;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PublicController extends CommonController
{
    public function triggerAction(string $command, Request $request, KernelInterface $kernel, ContainerInterface $container, LoggerInterface $logger): Response
    {
        $response  = new Response();
        $secretKey = $request->query->get('secret_key');
        $namespace = $request->query->get('namespace', 'cronfig');
        $config    = (new CoreParametersHelper($container))->get($namespace);

        $response->headers->set('Content-Type', 'text/plain');

        if (!$secretKey) {
            $message = 'secret key is missing in the request';
            $response->setStatusCode(Response::HTTP_FORBIDDEN);
            $logger->log('error', "Cronfig: {$message}");
            $response->setContent("error: {$message}");

            return $response;
        }

        if (empty($config['secret_key'])) {
            $response->setStatusCode(Response::HTTP_FORBIDDEN);
            $message = 'secret key is missing in the configuration';
            $logger->log('error', "Cronfig: {$message}");
            $response->setContent("error: {$message}");

            return $response;
        }
        
        if ($config['secret_key'] !== $secretKey) {
            $response->setStatusCode(Response::HTTP_FORBIDDEN);
            $logger->log('error', 'Cronfig: secret key mismatch: '.$config['secret_key'].' != '.$secretKey);
            $response->setContent('error: secret key mismatch');

            return $response;
        }

        $command    = explode(' ', urldecode($command));
        $errorCount = $request->get('error_count', 0);
        $args       = array_merge(['console'], $command);

        if ($errorCount > 2) {
            // Try to force the command if it failed 2 times before
            $args[] = '--force';
        }

        try {
            $input  = new ArgvInput($args);
            $output = new BufferedOutput();
            $app    = new Application($kernel);
            $app->setAutoExit(false);
            $app->run($input, $output);

            $output = "SUCCESS\n\n{$output->fetch()}";
        } catch (\Exception $exception) {
            $output = $exception->getMessage();
            $response->setStatusCode(500);
            $logger->log('error', 'Cronfig: '.$exception->getMessage());
        }

        // Guess that the output is an error message
        $errorWords = ['--force', 'exception'];

        foreach ($errorWords as $errorWord) {
            if (str_contains($output, $errorWord)) {
                $response->setStatusCode(500);
            }
        }

        $response->setContent($output);

        return $response;
    }
}
