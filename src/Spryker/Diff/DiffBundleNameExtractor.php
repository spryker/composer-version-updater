<?php

/**
 * Copyright © 2017-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Diff;

class DiffBundleNameExtractor
{

    /**
     * @var \Spryker\Diff\DiffReader
     */
    private $diffReader;

    /**
     * @param \Spryker\Diff\DiffReader $diffReader
     */
    public function __construct(DiffReader $diffReader)
    {
        $this->diffReader = $diffReader;
    }

    /**
     * @param string $pathToDiff
     *
     * @return array
     */
    public function getTouchedBundleNames($pathToDiff)
    {
        $diffs = $this->diffReader->getDiff($pathToDiff);

        $bundleList = [];
        foreach ($diffs as $diff) {
            if (preg_match('/Bundles\/(.*?)\//', $diff->getFrom(), $matches)) {
                $bundleList[] = $matches[1];
            }
            if (preg_match('/Bundles\/(.*?)\//', $diff->getTo(), $matches)) {
                $bundleList[] = $matches[1];
            }
        }

        $bundleList = array_unique($bundleList);

        return $bundleList;
    }

}
