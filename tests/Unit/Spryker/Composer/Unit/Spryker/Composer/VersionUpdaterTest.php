<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Unit\Spryker\Composer;

use PHPUnit_Framework_TestCase;
use Spryker\Composer\BundlesComposerJsonFinder;
use Spryker\Composer\VersionReader;
use Spryker\Composer\VersionUpdater;
use Spryker\Composer\VersionUpdaterInterface;

/**
 * @group Unit
 * @group Spryker
 * @group Composer
 * @group VersionUpdaterTest
 */
class VersionUpdaterTest extends PHPUnit_Framework_TestCase
{

    /**
     * @return void
     */
    public function setUp()
    {
        if (file_exists($this->getPathToComposerJson())) {
            unlink($this->getPathToComposerJson());
        }
        copy($this->getPathToComposerJsonDist(), $this->getPathToComposerJson());
    }

    /**
     * @return void
     */
    public function testVersionUpdaterCanBeInstantiatedWithoutBranchNameToUse()
    {
        $bundleComposerJsonFinder = $this->getBundleComposerJsonFinder();
        $versionUpdater = new VersionUpdater($bundleComposerJsonFinder, []);

        $this->assertInstanceOf(VersionUpdaterInterface::class, $versionUpdater);
    }

    /**
     * @return void
     */
    public function testVersionUpdaterCanBeInstantiatedWithBranchNameToUse()
    {
        $bundleComposerJsonFinder = $this->getBundleComposerJsonFinder();
        $versionUpdater = new VersionUpdater($bundleComposerJsonFinder, [], 'branch-name');

        $this->assertInstanceOf(VersionUpdaterInterface::class, $versionUpdater);
    }

    /**
     * @return void
     */
    public function testWhenBundleNotInComposerJsonConstraintIseIsNotUpdated()
    {
        $bundleComposerJsonFinder = $this->getBundleComposerJsonFinder();
        $bundleVersionInfo = [
            VersionReader::KEY_BUNDLE_NAME => 'PackageNotInComposerJson',
        ];
        $versionUpdater = new VersionUpdater($bundleComposerJsonFinder, [$bundleVersionInfo]);
        $versionUpdater->updateBundleVersionConstraints();

        $this->assertSame(
            file_get_contents($this->getPathToComposerJson()),
            file_get_contents($this->getPathToComposerJsonDist())
        );
    }

    /**
     * @return void
     */
    public function testWhenBundleIsMajorReleaseConstraintIsUpdatedWithNewVersion()
    {
        $bundleComposerJsonFinder = $this->getBundleComposerJsonFinder();
        $bundleVersionInfo = [
            VersionReader::KEY_BUNDLE_NAME => 'PackageA',
            VersionReader::KEY_VERSION_TYPE => 'major',
            VersionReader::KEY_VERSION_NEW => '2.0.0',
        ];
        $versionUpdater = new VersionUpdater($bundleComposerJsonFinder, [$bundleVersionInfo]);
        $versionUpdater->updateBundleVersionConstraints();

        $this->assertConstraint('spryker/package-a', '^2.0.0');
    }

    /**
     * @return void
     */
    public function testWhenBranchNameToUseIsDefinedConstraintIsUpdatedWithAliasAndOldVersion()
    {
        $bundleComposerJsonFinder = $this->getBundleComposerJsonFinder();
        $bundleVersionInfo = [
            VersionReader::KEY_BUNDLE_NAME => 'PackageA',
            VersionReader::KEY_VERSION_TYPE => 'major',
            VersionReader::KEY_VERSION_NEW => '2.0.0',
            VersionReader::KEY_VERSION_OLD => '1.2.3',
        ];
        $versionUpdater = new VersionUpdater($bundleComposerJsonFinder, [$bundleVersionInfo], 'dev-foo/bar');
        $versionUpdater->updateBundleVersionConstraints();

        $this->assertConstraint('spryker/package-a', 'dev-foo/bar as 1.2.3');
    }

    /**
     * @return void
     */
    public function testWhenChangedBundleIsMajorVersionAndComposerJsonFileIsForThisBundleUpdateBranchAlias()
    {
        $bundleComposerJsonFinder = $this->getBundleComposerJsonFinder();
        $bundleVersionInfo = [
            VersionReader::KEY_BUNDLE_NAME => 'TestPackage',
            VersionReader::KEY_VERSION_TYPE => 'major',
            VersionReader::KEY_VERSION_NEW => '2.0.0',
        ];
        $versionUpdater = new VersionUpdater($bundleComposerJsonFinder, [$bundleVersionInfo]);
        $versionUpdater->updateBundleVersionConstraints();

        $this->assertBranchAlias('2.0.x-dev');
    }

    /**
     * @return \Spryker\Composer\BundlesComposerJsonFinder
     */
    private function getBundleComposerJsonFinder()
    {
        return new BundlesComposerJsonFinder($this->getFixtureDir());
    }

    /**
     * @return string
     */
    private function getFixtureDir()
    {
        return __DIR__ . '/Fixtures/';
    }

    /**
     * @return string
     */
    private function getPathToComposerJson()
    {
        return $this->getFixtureDir() . '/composer.json';
    }

    /**
     * @return string
     */
    private function getPathToComposerJsonDist()
    {
        return $this->getPathToComposerJson() . '.dist';
    }

    /**
     * @param string $packageName
     * @param string $expectedVersionConstraint
     *
     * @return void
     */
    private function assertConstraint($packageName, $expectedVersionConstraint)
    {
        $composerJsonData = $this->getComposerJsonAsArray();

        $this->assertSame($composerJsonData['require'][$packageName], $expectedVersionConstraint);
    }

    /**
     * @param string $expectedBranchAlias
     *
     * @return void
     */
    private function assertBranchAlias($expectedBranchAlias)
    {
        $composerJsonData = $this->getComposerJsonAsArray();

        $this->assertSame($composerJsonData['extra']['branch-alias']['dev-master'], $expectedBranchAlias);
    }

    /**
     * @return array
     */
    private function getComposerJsonAsArray()
    {
        return json_decode(file_get_contents($this->getPathToComposerJson()), true);
    }

}
