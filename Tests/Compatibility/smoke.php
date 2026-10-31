<?php

declare(strict_types=1);

// Run inside an isolated Mautic installation: php plugins/CronfigBundle/Tests/Compatibility/smoke.php
// Boots Mautic but runs only help/list commands. Never runs cron, sends mail or writes configuration.
use Mautic\CoreBundle\Helper\CoreParametersHelper;
use MauticPlugin\CronfigBundle\Controller\PublicController;
use MauticPlugin\CronfigBundle\Model\CronfigModel;
use Psr\Log\AbstractLogger;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;

if ('cli' !== PHP_SAPI) {
    exit(1);
}

$root = dirname(__DIR__, 4);
$application = require $root.'/app/console-application.php';
$kernel = $application->getKernel();
$kernel->boot();
$container = $kernel->getContainer();
$checks = 0;
$check = static function (bool $condition, string $message) use (&$checks): void {
    if (!$condition) {
        throw new RuntimeException($message);
    }
    ++$checks;
};

// Only this in-memory container has fake keys; no local.php changes are needed.
$settings = new ContainerBuilder();
$settings->setParameter('mautic.cronfig', ['secret_key' => 'test-secret', 'api_key' => 'test-api']);
$settings->setParameter('mautic.site_url', 'https://mautic.example.invalid/');
$logger = new class extends AbstractLogger {
    public array $messages = [];
    public function log($level, $message, array $context = []): void
    {
        $this->messages[] = (string) $message;
    }
};
$controller = $container->get(PublicController::class);
foreach ([
    ['list --raw', [], 403, 'error: secret key is missing in the request'],
    ['list --raw', ['secret_key' => 'wrong-secret'], 403, 'error: secret key mismatch'],
    ['list --raw', ['secret_key' => 'test-secret', 'namespace' => 'cronfig_missing_test'], 403, 'error: secret key is missing in the configuration'],
    ['', ['secret_key' => 'test-secret'], 400, 'error: command is missing'],
    ['list --raw', ['secret_key' => 'test-secret'], 200, 'SUCCESS'],
    ['help "mautic:segments:update"', ['secret_key' => 'test-secret'], 200, 'SUCCESS'],
    ['help "mautic:segments:update"', ['secret_key' => 'test-secret', 'error_count' => 3], 200, 'SUCCESS'],
    ['cronfig:nonexistent-test-command', ['secret_key' => 'test-secret'], 500, 'ERROR'],
    ['list --cronfig-invalid-option', ['secret_key' => 'test-secret'], 500, 'ERROR'],
] as [$command, $query, $status, $prefix]) {
    $response = $controller->triggerAction($command, new Request($query), $kernel, $settings, $logger);
    $check($status === $response->getStatusCode(), 'Unexpected status for '.$command);
    $check(str_starts_with($response->getContent(), $prefix), 'Unexpected body for '.$command);
}
$check(!str_contains(implode('\n', $logger->messages), 'test-secret'), 'Configured key leaked into logs');
$check(!str_contains(implode('\n', $logger->messages), 'wrong-secret'), 'Request key leaked into logs');

$check($container->get(CronfigModel::class) instanceof CronfigModel, 'Model dependency injection failed');
$model = new CronfigModel(new CoreParametersHelper($settings), $container->get('mautic.configurator'), $kernel);
$commands = $model->getCommands();
$check(isset($commands['mautic:segments:update']), 'Missing essential segment task');
$check(isset($commands['messenger:consume email --time-limit=50 --limit=100']), 'Missing bounded email queue task');
$check(!isset($commands['mautic:emails:send']), 'Obsolete email task still advertised');
foreach ($commands as $command => $config) {
    $check($application->has(explode(' ', $command, 2)[0]), 'Unavailable command advertised');
}
foreach ($model->getCommandsWithUrls() as $config) {
    $check(str_starts_with($config['url'], 'https://mautic.example.invalid/cronfig/'), 'Wrong task URL');
    $check(str_ends_with($config['url'], '?secret_key=test-secret'), 'Missing task key');
}

// Render the actual Twig template against a minimal parent, with no browser/external script execution.
$twig = new Twig\Environment(new Twig\Loader\ChainLoader([
    new Twig\Loader\ArrayLoader(['@MauticCore/Default/content.html.twig' => '{% block content %}{% endblock %}']),
    new Twig\Loader\FilesystemLoader(__DIR__.'/../../Resources/views'),
]));
$twig->addFunction(new Twig\TwigFunction('includeStylesheet', static fn ($path) => ''));
$twig->addFunction(new Twig\TwigFunction('getAssetUrl', static fn ($path) => $path));
$twig->addFilter(new Twig\TwigFilter('trans', static fn ($value) => $value));
$html = $twig->render('index.html.twig', ['error' => null, 'commands' => $model->getCommandsWithUrls(), 'email' => "test'</script>@example.invalid", 'apiKey' => "test'</script>"]);
$check(!str_contains($html, "test'</script>"), 'Unsafe inline JavaScript escaping');
$check(str_contains($html, "searchParams.set('secret_key'"), 'Fresh task keys are not updated');
echo json_encode(['mautic' => $kernel->getVersion(), 'php' => PHP_VERSION, 'checks' => $checks, 'advertised_tasks' => count($commands), 'status' => 'passed'], JSON_PRETTY_PRINT)."\n";
