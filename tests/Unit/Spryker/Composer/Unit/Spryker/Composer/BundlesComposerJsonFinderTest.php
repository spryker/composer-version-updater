<?php

/**
 * Copyright © 2017-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Unit\Spryker\Composer;

use Spryker\Composer\BundlesComposerJsonFinder;
use Symfony\Component\Finder\Finder;
use PHPUnit_Framework_TestCase;

class BundlesComposerJsonFinderTest extends PHPUnit_Framework_TestCase
{

    /**
     * @return void
     */
    public function testGetComposerJsonFileReturnsIterateableFinder()
    {
        $bundlesComposerJsonFinder = new BundlesComposerJsonFinder(__DIR__);

        $this->assertInstanceOf(Finder::class, $bundlesComposerJsonFinder->getComposerJsonFiles());
    }

}
