<?php

/**
 * Copyright © 2017-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Composer;

use Symfony\Component\Finder\Finder;

class BundlesComposerJsonFinder
{

    /**
     * @var string
     */
    private $bundlesDirectory;

    /**
     * @param string $bundlesDirectory
     */
    public function __construct($bundlesDirectory)
    {
        $this->bundlesDirectory = $bundlesDirectory;
    }

    /**
     * @return \Symfony\Component\Finder\Finder
     */
    public function getComposerJsonFiles()
    {
        $finder = new Finder();
        $finder->in($this->bundlesDirectory)->name('composer.json');

        return $finder;
    }

}
