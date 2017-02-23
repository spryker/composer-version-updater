<?php

define('APPLICATION_ROOT', realpath(__DIR__ . '/..'));
define('SPRYKER_VENDOR_DIR', realpath(APPLICATION_ROOT . '/../spryker/Bundles'));
define('PROJECT_ROOT', realpath(APPLICATION_ROOT . '/../../..'));

require_once APPLICATION_ROOT . '/vendor/autoload.php';

use Spryker\Command\ComposerJsonValidator;
use Spryker\Command\PullRequestBundleValidator;
use Spryker\Command\ConstraintUpdater;
use Spryker\Composer\BundleNamesFinder;
use Symfony\Component\Console\Application;

$constraintUpdaterCommand = new ConstraintUpdater();

$bundleNamesFinder = new BundleNamesFinder();
$application = new Application();
$application
    ->addCommands([
        new ConstraintUpdater(),
        new PullRequestBundleValidator(),
        new ComposerJsonValidator($bundleNamesFinder),
    ])
;

$application->run();
