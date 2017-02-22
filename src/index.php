<?php

define('APPLICATION_ROOT', realpath(__DIR__ . '/..'));
define('SPRYKER_VENDOR_DIR', realpath(APPLICATION_ROOT . '/../spryker/Bundles'));
define('PROJECT_ROOT', realpath(APPLICATION_ROOT . '/../../..'));

require_once APPLICATION_ROOT . '/vendor/autoload.php';

use Spryker\Command\ComposerJsonValidator;
use Spryker\Command\PrBundleValidator;
use Spryker\Command\ConstraintUpdater;
use Symfony\Component\Console\Application;

$constraintUpdaterCommand = new ConstraintUpdater();

$application = new Application();
$application
    ->addCommands([
        new ConstraintUpdater(),
        new PrBundleValidator(),
        new ComposerJsonValidator(),
    ])
;

$application->run();
