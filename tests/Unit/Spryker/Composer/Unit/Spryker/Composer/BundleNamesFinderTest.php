<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Unit\Spryker\Composer;

use PHPUnit_Framework_TestCase;
use Spryker\Composer\BundleNamesFinder;
use Symfony\Component\Finder\Finder;

/**
 * @group Unit
 * @group Spryker
 * @group Composer
 * @group BundleNamesFinderTest
 */
class BundleNamesFinderTest extends PHPUnit_Framework_TestCase
{

    /**
     * @return void
     */
    public function testGetBundleNamesReturnsIterateableFinder()
    {
        $bundlesComposerJsonFinder = new BundleNamesFinder();

        $this->assertInstanceOf(Finder::class, $bundlesComposerJsonFinder->getBundleNames(__DIR__));
    }

}
