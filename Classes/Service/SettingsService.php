<?php
namespace Mfc\MfcBeloginCaptcha\Service;

/**
 * This file is developed by Marketing Factory Consulting GmbH.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

/**
 * Provide a way to get the configuration just everywhere
 *
 * Example:
 *   $pluginSettings = GeneralUtility::makeInstance(SettingsService::class)->getSettings();
 */
class SettingsService implements \TYPO3\CMS\Core\SingletonInterface
{
    /**
     * @var array
     */
    protected $settings = null;

    /**
     * Returns all settings.
     *
     * @return array
     */
    public function getSettings()
    {
        if (is_null($this->settings)) {
            if (class_exists(\TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class)) {
                $this->settings = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
                    \TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class
                )->get('mfc_belogin_captcha');
            } else {
                $this->settings = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['mfc_belogin_captcha']);
            }
        }
        return $this->settings;
    }

    /**
     * Returns the settings at path $path, which is separated by ".",
     * e.g. "pages.uid".
     * "pages.uid" would return $this->settings['pages']['uid'].
     *
     * If the path is invalid or no entry is found, false is returned.
     *
     * @param string $path
     *
     * @return mixed
     */
    public function getByPath($path)
    {
        return \TYPO3\CMS\Extbase\Reflection\ObjectAccess::getPropertyPath($this->getSettings(), $path);
    }
}
