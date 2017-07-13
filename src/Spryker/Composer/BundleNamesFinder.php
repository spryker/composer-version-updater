<?php

/**
 * Copyright © 2017-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Composer;

use Symfony\Component\Finder\Finder;

class BundleNamesFinder
{

    /**
     * @param string $bundlesDirectory
     *
     * @return \Symfony\Component\Finder\Finder
     */
    public function getBundleNames($bundlesDirectory)
    {
        $finder = new Finder();
        $finder->in($bundlesDirectory)->directories()->depth('0')->sortByName();

        return $finder;
    }

}
