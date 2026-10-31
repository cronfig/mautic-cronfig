<?php

declare(strict_types=1);

namespace MauticPlugin\CronfigBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Console\Input\StringInput;
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
        
        if (!is_string($config['secret_key']) || !hash_equals($config['secret_key'], $secretKey)) {
            $response->setStatusCode(Response::HTTP_FORBIDDEN);
            $logger->log('error', 'Cronfig: secret key mismatch');
            $response->setContent('error: secret key mismatch');

            return $response;
        }

        // Decode legacy Cronfig URLs, then let Symfony parse quoted arguments.
        $command = trim(urldecode($command));
        if ('' === $command) {
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            $response->setContent('error: command is missing');

            return $response;
        }

        try {
            $input = new StringInput($command);
            $input->setInteractive(false);
            $output = new BufferedOutput();
            $app = new Application($kernel);
            $app->setAutoExit(false);

            // Only commands that define --force can retry with that option.
            if ($request->query->getInt('error_count') > 2
                && $app->find($input->getFirstArgument())->getDefinition()->hasOption('force')
                && !$input->hasParameterOption('--force')) {
                $input = new StringInput($command.' --force');
                $input->setInteractive(false);
            }

            $exitCode = $app->run($input, $output);
            $response->setStatusCode(0 === $exitCode ? Response::HTTP_OK : Response::HTTP_INTERNAL_SERVER_ERROR);
            $response->setContent((0 === $exitCode ? "SUCCESS\n\n" : "ERROR\n\n").$output->fetch());
        } catch (\Throwable $exception) {
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $response->setContent("ERROR\n\n".$exception->getMessage());
            $logger->error('Cronfig: '.$exception->getMessage());
        }

        return $response;
    }
}
