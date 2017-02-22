<?php

/**
 * Copyright © 2017-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Command;

use Spryker\Composer\BundlesComposerJsonFinder;
use Spryker\Composer\VersionReader;
use Spryker\Composer\VersionUpdater;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ConstraintUpdater extends Command
{

    const OPTION_BRANCH = 'branch';
    const OPTION_BRANCH_SHORT = 'b';

    /**
     * @var \Symfony\Component\Console\Input\InputInterface
     */
    protected $input;

    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $output;

    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('spryker:constraint-updater')
            ->setDescription('Updates constraints in composer.json files of all bundles.')
            ->addOption(
                static::OPTION_BRANCH,
                static::OPTION_BRANCH_SHORT,
                InputOption::VALUE_OPTIONAL,
                'Set this if you want a branch name and "as {version}". E.g. -b dev-foo/bar will update then to "vendor/package": "dev-foo/bar as {version}"',
                ''
            );
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;

        $versionReader = new VersionReader(APPLICATION_ROOT . '/data/versions.txt');
        $bundleVersions = $versionReader->getBundleVersions();

        $majorBundles = $this->getMajorBundles($bundleVersions);
        $majorPackages = $this->getMajorPackageNames($majorBundles);

        $output->writeln('Validate your declared types...');

        if (!$this->validateDeclaredTypes($majorPackages, $bundleVersions)) {
            $output->writeln(PHP_EOL . '<fg=red>Found bugs in the declared types, please update the declared versions for the mentioned bundles.</>');

            return 1;
        }

        $output->writeln(PHP_EOL . '<fg=green>Nice! Looks like all your declared types are correct.</>');

        $output->writeln('Update your composer.json\'s...');
        $bundlesComposerJsonFinder = new BundlesComposerJsonFinder(SPRYKER_VENDOR_DIR);
        $bundleVersionUpdater = new VersionUpdater($bundlesComposerJsonFinder, $bundleVersions, $input->getOption(static::OPTION_BRANCH));
        $bundleVersionUpdater->updateBundleVersionConstraints();

        return 0;
    }

    /**
     * @param array $bundleVersions
     *
     * @return array
     */
    private function getMajorBundles(array $bundleVersions)
    {
        $callback = function ($bundleVersionInfo) {
            return ($bundleVersionInfo['declaredType'] === 'major');
        };

        return array_filter($bundleVersions, $callback);
    }

    /**
     * @param array $majorBundles
     *
     * @return array
     */
    private function getMajorPackageNames(array $majorBundles)
    {
        $majorPackages = [];
        foreach ($majorBundles as $majorBundle) {
            $majorPackages[] = $majorBundle['package'];
        }

        return $majorPackages;
    }

    /**
     * @param array $majorPackages
     * @param array $bundleVersions
     *
     * @return bool
     */
    private function validateDeclaredTypes(array $majorPackages, array $bundleVersions)
    {
        $declaredTypesValid = true;
        foreach ($bundleVersions as $bundleVersion) {
            if ($bundleVersion['declaredType'] === 'major') {
                continue;
            }
            $composerRequire = $this->loadRequiredPackages($bundleVersion['bundle']);
            if (!is_array($composerRequire)) {
                continue;
            }
            $result = array_intersect($majorPackages, $composerRequire);

            if (count($result) !== 0) {
                $declaredTypesValid = false;

                $this->output->writeln(sprintf(
                    '"<fg=green>%s</>" is declared as "<fg=green>%s</>" but is uses "<fg=green>%s</>" which %s marked as major',
                    $bundleVersion['bundle'],
                    $bundleVersion['declaredType'],
                    implode(', ', $result),
                    count($result) > 1 ? 'are' : 'is'
                ));
            }
        }

        return $declaredTypesValid;
    }

    /**
     * @param string $bundle
     *
     * @return array|bool
     */
    private function loadRequiredPackages($bundle)
    {
        $pathToComposerFile = SPRYKER_VENDOR_DIR . '/' . $bundle . '/composer.json';
        $composer = json_decode(file_get_contents($pathToComposerFile), true);

        if (!isset($composer['require'])) {
            return false;
        }

        return array_keys($composer['require']);
    }

}
