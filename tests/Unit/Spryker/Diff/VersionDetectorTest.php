<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Unit\Spryker\Diff;

use PHPUnit_Framework_TestCase;
use Spryker\Diff\DiffBundleNameExtractor;
use Spryker\Diff\DiffReader;
use Spryker\Diff\VersionDetector;

/**
 * @group Unit
 * @group Spryker
 * @group Diff
 * @group VersionDetectorTest
 */
class VersionDetectorTest extends PHPUnit_Framework_TestCase
{

    /**
     * @return void
     */
    public function testCanBeConstructedWithDiffReaderAndTouchedBundleNameFinder()
    {
        $diffReader = new DiffReader($this->getFixtureDirectory());
        $touchedBundleNameExtractor = new DiffBundleNameExtractor($diffReader);

        $versionDetector = new VersionDetector($diffReader);
    }

    /**
     * @return string
     */
    protected function getFixtureDirectory()
    {
        return __DIR__ . '/Fixtures';
    }

}
