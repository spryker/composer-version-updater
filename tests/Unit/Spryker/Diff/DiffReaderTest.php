<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Unit\Spryker\Diff;

use PHPUnit_Framework_TestCase;
use Spryker\Diff\DiffReader;
use Spryker\Diff\Exception\InvalidPathToDiffException;

/**
 * @group Unit
 * @group Spryker
 * @group ConstraintFinder
 * @group DiffReaderTest
 */
class DiffReaderTest extends PHPUnit_Framework_TestCase
{

    /**
     * @return void
     */
    public function testInvalidPathToDiffFileThrowsException()
    {
        $this->expectException(InvalidPathToDiffException::class);

        $diffReader = new DiffReader();
        $diffReader->getDiff('invalidFilePath');
    }

    /**
     * @return void
     */
    public function testGetDiffReturnArrayWithDiff()
    {
        $diffReader = new DiffReader();

        $this->assertInternalType('array', $diffReader->getDiff($this->getPathToDiff()));
    }

    /**
     * @return void
     */
    public function testGetDiffContainsAtLeastOnDiff()
    {
        $diffReader = new DiffReader();

        $diffs = $diffReader->getDiff($this->getPathToDiff());
        $this->assertTrue(count($diffs) > 0);
    }

    /**
     * @return string
     */
    private function getPathToDiff()
    {
        return __DIR__ . '/Fixtures/master.diff';
    }

}
