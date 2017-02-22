<?php

/**
 * Copyright © 2017-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Unit\Spryker\Composer;

use Spryker\Composer\BundleNamesFinder;
use Symfony\Component\Finder\Finder;
use PHPUnit_Framework_TestCase;

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
