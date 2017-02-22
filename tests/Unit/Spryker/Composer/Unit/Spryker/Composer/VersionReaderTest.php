<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Unit\Spryker\Composer;

use PHPUnit_Framework_TestCase;
use Spryker\Composer\Exception\InvalidPathToVersionFileException;
use Spryker\Composer\VersionReader;
use Spryker\Composer\VersionReaderInterface;

/**
 * @group Unit
 * @group Spryker
 * @group Composer
 * @group VersionReaderTest
 */
class VersionReaderTest extends PHPUnit_Framework_TestCase
{

    /**
     * @return void
     */
    public function testVersionReaderCanBeInstantiatedWithValidPathToVersionFile()
    {
        $this->assertInstanceOf(VersionReaderInterface::class, new VersionReader($this->getPathToVersionFile()));
    }

    /**
     * @return void
     */
    public function testIfPathToVersionFileIsInvalidVersionReaderThrowsExceptionWhenInstantiated()
    {
        $this->expectException(InvalidPathToVersionFileException::class);
        $this->assertInstanceOf(VersionReaderInterface::class, new VersionReader('invalid file path'));
    }

    /**
     * @return void
     */
    public function testGetBundleVersionsReturnsArray()
    {
        $versionReader = new VersionReader($this->getPathToVersionFile());

        $this->assertInternalType('array', $versionReader->getBundleVersions());
    }

    /**
     * @return void
     */
    public function testGetBundleVersionsDetectsMajor()
    {
        $versionReader = new VersionReader($this->getPathToVersionFile());

        $bundleInformationCollection = $versionReader->getBundleVersions();

        $majorBundle = $bundleInformationCollection[0];
        $this->assertSame('Application', $majorBundle[VersionReader::KEY_BUNDLE_NAME]);
        $this->assertSame('major', $majorBundle[VersionReader::KEY_VERSION_TYPE]);
    }

    /**
     * @return void
     */
    public function testGetBundleVersionsDetectsMinor()
    {
        $versionReader = new VersionReader($this->getPathToVersionFile());

        $bundleInformationCollection = $versionReader->getBundleVersions();

        $minorBundle = $bundleInformationCollection[1];
        $this->assertSame('Kernel', $minorBundle[VersionReader::KEY_BUNDLE_NAME]);
        $this->assertSame('minor', $minorBundle[VersionReader::KEY_VERSION_TYPE]);
    }

    /**
     * @return void
     */
    public function testGetBundleVersionsDetectsPatch()
    {
        $versionReader = new VersionReader($this->getPathToVersionFile());

        $bundleInformationCollection = $versionReader->getBundleVersions();

        $patchBundle = $bundleInformationCollection[2];
        $this->assertSame('Console', $patchBundle[VersionReader::KEY_BUNDLE_NAME]);
        $this->assertSame('patch', $patchBundle[VersionReader::KEY_VERSION_TYPE]);
    }

    /**
     * @return string
     */
    private function getPathToVersionFile()
    {
        return __DIR__ . '/Fixtures/version_file.txt';
    }
}
