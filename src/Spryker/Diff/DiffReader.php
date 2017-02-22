<?php

/**
 * Copyright © 2017-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Diff;

use SebastianBergmann\Diff\Parser;
use Spryker\Diff\Exception\InvalidPathToDiffException;

class DiffReader implements DiffReaderInterface
{

    /**
     * @param string $pathToDiff
     *
     * @return \SebastianBergmann\Diff\Diff[]
     */
    public function getDiff($pathToDiff)
    {
        $this->validatePathToDiff($pathToDiff);

        return $this->getDiffReader()->parse(file_get_contents($pathToDiff));
    }

    /**
     * @return \SebastianBergmann\Diff\Parser
     */
    private function getDiffReader()
    {
        return new Parser();
    }

    /**
     * @param string $pathToDiff
     *
     * @throws \Spryker\Diff\Exception\InvalidPathToDiffException
     *
     * @return void
     */
    private function validatePathToDiff($pathToDiff)
    {
        if (!is_file($pathToDiff)) {
            throw new InvalidPathToDiffException(sprintf('Path to your diff file is invalid, i got "%s"', $pathToDiff));
        }
    }

}
