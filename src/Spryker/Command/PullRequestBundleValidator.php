<?php

/**
 * Copyright © 2017-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Command;

use Spryker\Composer\VersionReader;
use Spryker\Diff\DiffBundleNameExtractor;
use Spryker\Diff\DiffReader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\VarDumper\VarDumper;

class PullRequestBundleValidator extends Command
{

    const COMMAND_NAME = 'spryker:pr-module-validator';

    const OPTION_PATH_TO_DIFF = 'path-to-diff';
    const OPTION_PATH_TO_DIFF_SHORT = 'a';

    const OPTION_PATH_TO_VERSION_FILE = 'path-to-version-file';
    const OPTION_PATH_TO_VERSION_FILE_SHORT = 'b';

    const SUCCESS_MESSAGE = '<fg=green>Your Pull Request template is complete and does not contain invalid bundles.</>';
    const ERROR_MESSAGE = '<fg=red>Please check all the printed bundles and update your pull-request and run the process again.</>';
    const ERROR_MESSAGE_MISSING_IN_PULL_REQUEST = '"<fg=green>%s</>" is missing in your PR template.';
    const ERROR_MESSAGE_TO_MUCH_IN_PULL_REQUEST = '"<fg=green>%s</>" should not be in your PR template.';

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
            ->setName(static::COMMAND_NAME)
            ->setDescription('Validate if all bundles found in diff file are present in your pull-request.')
            ->addOption(
                static::OPTION_PATH_TO_DIFF,
                static::OPTION_PATH_TO_DIFF_SHORT,
                InputOption::VALUE_REQUIRED,
                'Path to your diff file (create one locally with "$ git diff >> /path/to/diff/file").',
                APPLICATION_ROOT . '/data/master.diff'
            )
            ->addOption(
                static::OPTION_PATH_TO_VERSION_FILE,
                static::OPTION_PATH_TO_VERSION_FILE_SHORT,
                InputOption::VALUE_REQUIRED,
                'Path to your version file (download: http://releases-spryker-com.herokuapp.com/release/pull-request/{your pr number}).',
                APPLICATION_ROOT . '/data/versions.txt'
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

        if (!$this->validatePullRequestContainsAllBundles() || !$this->validatePullRequestContainsNotMoreBundles()) {
            $output->writeln(static::ERROR_MESSAGE);

            return 1;
        }

        $output->writeln(static::SUCCESS_MESSAGE);

        return 0;
    }

    /**
     * @return bool
     */
    protected function validatePullRequestContainsAllBundles()
    {
        $touchedBundleNames = $this->getBundleNamesFromDiff();
        echo '<pre>' . PHP_EOL . VarDumper::dump($touchedBundleNames) . PHP_EOL . 'Line: ' . __LINE__ . PHP_EOL . 'File: ' . __FILE__;
        die(); // Remove?
        $prTemplateBundleNames = $this->getBundleNamesFromPullRequest();

        $bundleCollection = array_diff($touchedBundleNames, $prTemplateBundleNames);

        $isValid = true;
        foreach ($bundleCollection as $missingInPrTemplate) {
            $isValid = false;
            $this->output->writeln(sprintf(static::ERROR_MESSAGE_MISSING_IN_PULL_REQUEST, $missingInPrTemplate));
        }

        return $isValid;
    }

    /**
     * @return bool
     */
    protected function validatePullRequestContainsNotMoreBundles()
    {
        $touchedBundleNames = $this->getBundleNamesFromDiff();
        $prTemplateBundleNames = $this->getBundleNamesFromPullRequest();

        $bundleCollection = array_diff($prTemplateBundleNames, $touchedBundleNames);
        $isValid = true;
        foreach ($bundleCollection as $missingInDiff) {
            $isValid = false;
            $this->output->writeln(sprintf(static::ERROR_MESSAGE_TO_MUCH_IN_PULL_REQUEST, $missingInDiff));
        }

        return $isValid;
    }

    /**
     * @return array
     */
    protected function getBundleNamesFromDiff()
    {
        $pathToDiffFile = $this->input->getOption(static::OPTION_PATH_TO_DIFF);

        $touchedBundleNameExtractor = new DiffBundleNameExtractor(
            new DiffReader()
        );

        $bundleNamesFromDiff = $touchedBundleNameExtractor->getTouchedBundleNames($pathToDiffFile);

        sort($bundleNamesFromDiff);

        return $bundleNamesFromDiff;
    }

    /**
     * @return array
     */
    protected function getBundleNamesFromPullRequest()
    {
        $pathToVersionFile = $this->input->getOption(static::OPTION_PATH_TO_VERSION_FILE);

        $versionReader = new VersionReader($pathToVersionFile);
        $bundleVersions = $versionReader->getBundleVersions();

        $bundleNamesInPrTemplate = [];

        foreach ($bundleVersions as $bundleVersion) {
            $bundleNamesInPrTemplate[] = $bundleVersion['bundle'];
        }

        sort($bundleNamesInPrTemplate);

        return $bundleNamesInPrTemplate;
    }

}
