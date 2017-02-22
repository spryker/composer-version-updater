<?php

/**
 * Copyright © 2017-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Command;

use Spryker\Composer\VersionReader;
use Spryker\Diff\DiffReader;
use Spryker\Diff\DiffBundleNameExtractor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PrBundleValidator extends Command
{

    const OPTION_PATH_TO_DIFF = 'path-to-diff';
    const OPTION_PATH_TO_DIFF_SHORT = 'a';

    const OPTION_PATH_TO_VERSION_FILE = 'path-to-version-file';
    const OPTION_PATH_TO_VERSION_FILE_SHORT = 'b';

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
            ->setName('spryker:pr-bundle-validator')
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
            )
        ;
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

        $touchedBundleNames = $this->getTouchedBundleNames();
        $prTemplateBundleNames = $this->getPrBundleNames();

        $bundleCollection = array_diff($touchedBundleNames, $prTemplateBundleNames);

        $hasMissingBundles = false;
        foreach ($bundleCollection as $missingInPrTemplate) {
            $hasMissingBundles = true;
            $output->write(sprintf('"<fg=green>%s</>" is missing in your PR template.', $missingInPrTemplate));
        }

        if ($hasMissingBundles) {
            $output->write('Please add all the printed bundles to your pull-request and run the process again.');

            return 1;
        }

        return 0;
    }

    /**
     * @return array
     */
    protected function getTouchedBundleNames()
    {
        $pathToDiffFile = $this->input->getOption(static::OPTION_PATH_TO_DIFF);

        $touchedBundleNameExtractor = new DiffBundleNameExtractor(
            new DiffReader()
        );

        return $touchedBundleNameExtractor->getTouchedBundleNames($pathToDiffFile);
    }

    /**
     * @return array
     */
    private function getPrBundleNames()
    {
        $pathToVersionFile = $this->input->getOption(static::OPTION_PATH_TO_VERSION_FILE);

        $versionReader = new VersionReader($pathToVersionFile);
        $bundleVersions = $versionReader->getBundleVersions();

        $bundleNamesInPrTemplate = [];

        foreach ($bundleVersions as $bundleVersion) {
            $bundleNamesInPrTemplate[] = $bundleVersion['bundle'];
        }

        return $bundleNamesInPrTemplate;
    }

}
