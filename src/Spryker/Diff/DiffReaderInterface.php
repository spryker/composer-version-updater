<?php

/**
 * Copyright © 2017-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Diff;

interface DiffReaderInterface
{

    /**
     * @param string $pathToDiff
     *
     * @return \SebastianBergmann\Diff\Diff[]
     */
    public function getDiff($pathToDiff);

}
