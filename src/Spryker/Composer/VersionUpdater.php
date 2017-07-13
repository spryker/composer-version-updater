<?php

/**
 * Copyright © 2017-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Composer;

use Zend\Filter\Word\CamelCaseToDash;

class VersionUpdater implements VersionUpdaterInterface
{

    /**
     * @var \Spryker\Composer\BundlesComposerJsonFinder
     */
    private $bundlesComposerJsonFinder;

    /**
     * @var string
     */
    private $bundlesVersionInfo;

    /**
     * @var string
     */
    private $branchName;

    /**
     * @param \Spryker\Composer\BundlesComposerJsonFinder $bundlesComposerJsonFinder
     * @param array $bundlesVersionInfo
     * @param string $branchName
     */
    public function __construct(BundlesComposerJsonFinder $bundlesComposerJsonFinder, array $bundlesVersionInfo, $branchName = '')
    {
        $this->bundlesComposerJsonFinder = $bundlesComposerJsonFinder;
        $this->bundlesVersionInfo = $bundlesVersionInfo;
        $this->branchName = $branchName;
    }

    /**
     * @return array
     */
    public function updateBundleVersionConstraints()
    {
        foreach ($this->getComposerFiles() as $composerJson) {
            $composerJsonContent = $composerJson->getContents();
            $composerJsonContent = $this->updateComposerJsonContent($composerJsonContent);

            if ($composerJsonContent !== $composerJson->getContents()) {
                file_put_contents($composerJson->getPathname(), $composerJsonContent);
            }
        }
    }

    /**
     * @return \Symfony\Component\Finder\Finder
     */
    private function getComposerFiles()
    {
        return $this->bundlesComposerJsonFinder->getComposerJsonFiles();
    }

    /**
     * @param string $composerJsonContent
     *
     * @return string
     */
    private function updateComposerJsonContent($composerJsonContent)
    {
        foreach ($this->bundlesVersionInfo as $bundleVersionInfo) {
            $packageName = $this->getPackageName($bundleVersionInfo['bundle']);

            if ($this->isPackageInComposerJsonRequired($composerJsonContent, $packageName)) {

                $newConstraint = $this->buildNewConstraint($bundleVersionInfo);

                $search = sprintf('"%s": "(.*?)([0-9]).([0-9]).([0-9])"', $packageName);
                $replace = sprintf('"%s": "%s"', $packageName, $newConstraint);

                $composerJsonContent = preg_replace('#' . $search . '#', $replace, $composerJsonContent);

                $packageNameSearch = sprintf('"name": "%s"', $packageName);
                $branchAliasSearch = '"dev-master": "(.*?).0.x-dev"';

                if (preg_match('#' . $branchAliasSearch . '#', $composerJsonContent) && preg_match('#' . $packageNameSearch . '#', $composerJsonContent) && $this->isMajorBump($bundleVersionInfo)) {
                    $nextMajorVersion = $bundleVersionInfo[VersionReader::KEY_VERSION_NEW][0];
                    $branchAliasReplace = sprintf('"dev-master": "%d.0.x-dev"', $nextMajorVersion);
                    $composerJsonContent = preg_replace('#' . $branchAliasSearch . '#', $branchAliasReplace, $composerJsonContent);
                }
            }
        }

        return $composerJsonContent;
    }

    /**
     * @param string $bundleName
     *
     * @return string
     */
    private function getPackageName($bundleName)
    {
        $filter = new CamelCaseToDash();
        $packagerName = 'spryker/' . strtolower($filter->filter($bundleName));

        return $packagerName;
    }

    /**
     * @param string $composerJsonContent
     * @param string $packageName
     *
     * @return int
     */
    private function isPackageInComposerJsonRequired($composerJsonContent, $packageName)
    {
        return preg_match('/' . preg_quote($packageName, '/') . '/', $composerJsonContent);
    }

    /**
     * If branch name is set we need to add it as version constraint and use an alias with the old version.
     * We need to use the old version here because the new version may not exists. This is the case when we
     * change composer.json's to be installable in demoshop (split).
     *
     * @param array $bundleVersionInfo
     *
     * @return string
     */
    private function buildNewConstraint(array $bundleVersionInfo)
    {
        if ($this->branchName) {
            return $this->branchName . ' as ' . $bundleVersionInfo[VersionReader::KEY_VERSION_OLD];
        }

        return '^' . $bundleVersionInfo[VersionReader::KEY_VERSION_NEW];
    }

    /**
     * @param array $bundleVersionInfo
     *
     * @return bool
     */
    private function isMajorBump(array $bundleVersionInfo)
    {
        return ($bundleVersionInfo[VersionReader::KEY_VERSION_TYPE] === 'major');
    }

}
