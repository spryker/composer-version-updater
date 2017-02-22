<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Unit\Spryker\Diff;

use PHPUnit_Framework_TestCase;
use Spryker\Diff\DiffBundleNameExtractor;
use Spryker\Diff\DiffReader;

/**
 * @group Unit
 * @group Spryker
 * @group Diff
 * @group BundleNamesTest
 */
class DiffBundleNameExtractorTest extends PHPUnit_Framework_TestCase
{

    /**
     * @return string
     */
    protected function getDiffFilePath()
    {
        return __DIR__ . '/Fixtures/master.diff';
    }

    /**
     * @return void
     */
    public function testCanBeConstructedWithDiffReader()
    {
        $diffReader = new DiffReader();
        $touchedBundleNameExtractor = new DiffBundleNameExtractor($diffReader);

        $this->assertInstanceOf(DiffBundleNameExtractor::class, $touchedBundleNameExtractor);
    }

    /**
     * @return void
     */
    public function testGetTouchedBundlesReturnsArray()
    {
        $diffReader = new DiffReader();
        $touchedBundleNameExtractor = new DiffBundleNameExtractor($diffReader);

        $this->assertInternalType('array', $touchedBundleNameExtractor->getTouchedBundleNames($this->getDiffFilePath()));
    }

    /**
     * @return void
     */
    public function testGetTouchedBundlesReturnsAtLeastOneBundleName()
    {
        $diffReader = new DiffReader();
        $touchedBundleNameExtractor = new DiffBundleNameExtractor($diffReader);

        $touchedBundleNames = $touchedBundleNameExtractor->getTouchedBundleNames($this->getDiffFilePath());
        $this->assertTrue(count($touchedBundleNames) > 0);

        $this->assertSame('Acl', $touchedBundleNames[0]);
    }

}
