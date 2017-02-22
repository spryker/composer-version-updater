<?php

/**
 * Copyright © 2017-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Composer;

use Spryker\Composer\Exception\InvalidPathToVersionFileException;
use Zend\Filter\Word\CamelCaseToDash;

class VersionReader implements VersionReaderInterface
{
    const KEY_BUNDLE_NAME = 'bundle';
    const KEY_PACKAGE_NAME = 'package';
    const KEY_VERSION_TYPE = 'declaredType';
    const KEY_VERSION_OLD = 'oldVersion';
    const KEY_VERSION_NEW = 'newVersion';

    /**
     * @var string
     */
    private $filePath;

    /**
     * @param string $filePath
     */
    public function __construct($filePath)
    {
        $this->validatePathToVersionFile($filePath);

        $this->filePath = $filePath;
    }

    /**
     * @return array
     */
    public function getBundleVersions()
    {
        $bundleVersionInfo = $this->parseVersionFile();

        return $bundleVersionInfo;
    }

    /**
     * @return array
     */
    private function parseVersionFile()
    {
        $bundleVersions = [];

        $fileContent = file_get_contents($this->filePath);
        preg_match_all('/(.*?)\s\s(major|minor|patch)\s\((.*)\s>>\s(.*)\)/', $fileContent, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $bundleVersions[] = $this->buildBundleVersion($match);
        }

        return $bundleVersions;
    }

    /**
     * @param array $match
     *
     * @return array
     */
    private function buildBundleVersion(array $match)
    {
        $filter = new CamelCaseToDash();

        return [
            static::KEY_BUNDLE_NAME => $match[1],
            static::KEY_PACKAGE_NAME => 'spryker/' . strtolower($filter->filter($match[1])),
            static::KEY_VERSION_TYPE => $match[2],
            static::KEY_VERSION_OLD => $match[3],
            static::KEY_VERSION_NEW => $match[4],
        ];
    }

    /**
     * @param string $filePath
     *
     * @throws \Spryker\Composer\Exception\InvalidPathToVersionFileException
     *
     * @return void
     */
    private function validatePathToVersionFile($filePath)
    {
        if (!is_file($filePath)) {
            throw new InvalidPathToVersionFileException(sprintf('"%s" is not a valid file path.', $filePath));
        }
    }

}
