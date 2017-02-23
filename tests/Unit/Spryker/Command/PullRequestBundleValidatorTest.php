<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Unit\Spryker\Command;

use PHPUnit_Framework_TestCase;
use Spryker\Command\PullRequestBundleValidator;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @group Unit
 * @group Spryker
 * @group Command
 * @group PullRequestBundleValidatorTest
 */
class PullRequestBundleValidatorTest extends PHPUnit_Framework_TestCase
{
    const BUNDLE_FROM_DIFF = 'BundleFromDiff';
    const BUNDLE_FROM_PULL_REQUEST = 'BundleFromPullRequest';

    /**
     * @return void
     */
    public function testWhenNoBundleMissingInPullRequestSuccessMessageIsPrinted()
    {
        $commandMock = $this->getCommandMock(['getBundleNamesFromDiff', 'getBundleNamesFromPullRequest']);
        $commandMock->method('getBundleNamesFromDiff')->willReturn([]);
        $commandMock->method('getBundleNamesFromPullRequest')->willReturn([]);

        $commandTester = $this->getCommandTester($commandMock);
        $commandTester->execute([]);

        $expectedMessage = $this->buildExpectedWriteln(PullRequestBundleValidator::SUCCESS_MESSAGE);

        $this->assertSame($expectedMessage, $commandTester->getDisplay());
    }

    /**
     * @return void
     */
    public function testIfOneBundleIsMissingInPullRequestErrorMessageIsPrinted()
    {
        $commandMock = $this->getCommandMock(['getBundleNamesFromDiff', 'getBundleNamesFromPullRequest']);
        $commandMock->method('getBundleNamesFromDiff')->willReturn([static::BUNDLE_FROM_DIFF]);
        $commandMock->method('getBundleNamesFromPullRequest')->willReturn([]);

        $commandTester = $this->getCommandTester($commandMock);
        $commandTester->execute([]);

        $expectedMessage = $this->buildExpectedWriteln(PullRequestBundleValidator::ERROR_MESSAGE_MISSING_IN_PULL_REQUEST, static::BUNDLE_FROM_DIFF);
        $expectedMessage .= $this->buildExpectedWriteln(PullRequestBundleValidator::ERROR_MESSAGE);

        $this->assertSame($expectedMessage, $commandTester->getDisplay());
    }

    /**
     * @return void
     */
    public function testIfOneBundleToMuchInPullRequestErrorMessageIsPrinted()
    {
        $commandMock = $this->getCommandMock(['getBundleNamesFromDiff', 'getBundleNamesFromPullRequest']);
        $commandMock->method('getBundleNamesFromDiff')->willReturn([]);
        $commandMock->method('getBundleNamesFromPullRequest')->willReturn([static::BUNDLE_FROM_PULL_REQUEST]);

        $commandTester = $this->getCommandTester($commandMock);
        $commandTester->execute([]);

        $expectedMessage = $this->buildExpectedWriteln(PullRequestBundleValidator::ERROR_MESSAGE_TO_MUCH_IN_PULL_REQUEST, static::BUNDLE_FROM_PULL_REQUEST);
        $expectedMessage .= $this->buildExpectedWriteln(PullRequestBundleValidator::ERROR_MESSAGE);

        $this->assertSame($expectedMessage, $commandTester->getDisplay());
    }

    /**
     * @param array $methods
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|PullRequestBundleValidator
     */
    private function getCommandMock(array $methods)
    {
        $mockBuilder = $this->getMockBuilder(PullRequestBundleValidator::class);
        $mockBuilder->setMethods($methods);

        return $mockBuilder->getMock();
    }

    /**
     * @param \Spryker\Command\PullRequestBundleValidator $commandMock
     *
     * @return \Symfony\Component\Console\Tester\CommandTester
     */
    private function getCommandTester(PullRequestBundleValidator $commandMock)
    {
        $application = new Application();
        $application->add($commandMock);

        $command = $application->find(PullRequestBundleValidator::COMMAND_NAME);
        $commandTester = new CommandTester($command);

        return $commandTester;
    }

    /**
     * Call this method with one or more arguments.
     * First argument must be the message to print.
     * If more then one argument is passed all other arguments
     * will be used in vsprintf.
     *
     * @param array ...$arguments
     *
     * @return string
     */
    private function buildExpectedWriteln(...$arguments)
    {
        $message = array_shift($arguments);

        if (count($arguments) > 0) {
            $message = vsprintf($message, $arguments);
        }

        $search = ['<fg=green>', '<fg=red>', '</>'];

        return str_replace($search, '', $message) . PHP_EOL;
    }


}
