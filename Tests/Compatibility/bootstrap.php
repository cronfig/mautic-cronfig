<?php

declare(strict_types=1);

$application = require dirname(__DIR__, 4).'/app/console-application.php';
$application->getKernel()->boot();
