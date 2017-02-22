<?php

/**
 * Copyright © 2017-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Unit\Spryker\Command;

use Spryker\Command\ComposerJsonValidator;
use PHPUnit_Framework_TestCase;
use Spryker\Composer\BundleNamesFinder;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class ComposerJsonValidatorTest extends PHPUnit_Framework_TestCase
{

    /**
     * @return void
     */
    public function testWhenNoComposerJsonCanBeFoundErrorMessagesIsPrinted()
    {
        $input = [
            '--' . ComposerJsonValidator::OPTION_PATH_TO_BUNDLES => __DIR__,
        ];

        $commandMock = $this->getCommandMock();
        $application = new Application();
        $application->add($commandMock);
        $command = $application->find('spryker:composer-validate');

        $tester = new CommandTester($command);
        $tester->execute($input);

        $expectedMessage = $this->buildExpectedMessages(sprintf(ComposerJsonValidator::MESSAGE_COULD_NOT_FIND_COMPOSER_JSON, __DIR__ . '/Fixtures'));

        $this->assertSame($expectedMessage, $tester->getDisplay());
    }

    /**
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|ComposerJsonValidator
     */
    private function getCommandMock()
    {
        $bundleNamesFinder = new BundleNamesFinder();
        $mockBuilder = $this->getMockBuilder(ComposerJsonValidator::class);
        $mockBuilder
            ->setConstructorArgs([$bundleNamesFinder])
            ->setMethods(['getBundleNames'])
        ;

        return $mockBuilder->getMock();
    }

    /**
     * @param string $message
     *
     * @return string mixed
     */
    private function buildExpectedMessages($message)
    {
        $search = [
            '<fg=green>',
            '<fg=red>',
            '<fg=yellow>',
            '</>',
        ];

        return str_replace($search, '', $message);
    }

}
