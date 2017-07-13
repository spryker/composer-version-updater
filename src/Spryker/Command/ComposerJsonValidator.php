<?php

/**
 * Copyright © 2017-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Command;

use Spryker\Composer\BundleNamesFinder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class ComposerJsonValidator extends Command
{

    const COMMAND_PATTERN = 'php composer.phar validate -d %s';
    const OPTION_PATH_TO_BUNDLES = 'path-to-bundles';
    const PATH_TO_BUNDLES_SHORT = 'b';
    const MESSAGE_COULD_NOT_FIND_COMPOSER_JSON = 'Could not find a composer.json file in "<fg=green>%s</>"';

    /**
     * @var InputInterface
     */
    private $input;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var BundleNamesFinder
     */
    private $bundleNamesFinder;

    /**
     * @param \Spryker\Composer\BundleNamesFinder $bundleNamesFinder
     * @param string|null $name
     */
    public function __construct(BundleNamesFinder $bundleNamesFinder, $name = null)
    {
        parent::__construct($name);

        $this->bundleNamesFinder = $bundleNamesFinder;
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('spryker:composer-validate')
            ->setDescription('Validate all composer.json files.')
            ->addOption(
                static::OPTION_PATH_TO_BUNDLES,
                static::PATH_TO_BUNDLES_SHORT,
                InputOption::VALUE_OPTIONAL,
                'Path to bundles',
                SPRYKER_VENDOR_DIR
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

        foreach ($this->getBundleNames() as $bundleFileInfo) {
            if (file_exists($bundleFileInfo->getPathname() . '/composer.json')) {
                $this->runComposerValidate($bundleFileInfo);
            } else {
                $output->writeln('<fg=red>' . sprintf(static::MESSAGE_COULD_NOT_FIND_COMPOSER_JSON, $bundleFileInfo->getPathname()) . '</>');
            }
        }

        return 0;
    }

    /**
     * @return \Symfony\Component\Finder\Finder
     */
    private function getBundleNames()
    {
        $bundlesDirectory = $this->input->getOption(static::OPTION_PATH_TO_BUNDLES);

        return $this->bundleNamesFinder->getBundleNames($bundlesDirectory);
    }

    /**
     * @param SplFileInfo $bundleFileInfo
     *
     * @return void
     */
    protected function runComposerValidate(SplFileInfo $bundleFileInfo)
    {
        if ($this->input->getOption('verbose')) {
            $this->output->write('Checking: <fg=green>' . $bundleFileInfo->getFilename() . '</>');
        }

        $process = new Process(sprintf(static::COMMAND_PATTERN, $bundleFileInfo->getPathname()), PROJECT_ROOT);

        try {
            $process->mustRun();
            if ($this->input->getOption('verbose')) {
                $this->output->write($process->getOutput());
            }
        } catch (ProcessFailedException $e) {
            $this->output->write('<fg=red>' . $e->getMessage() . '</>');
        }
    }


}
