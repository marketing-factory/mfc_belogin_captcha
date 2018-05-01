<?php
namespace Mfc\MfcBeloginCaptcha\Service;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Sebastian Fischer <typo@marketing-factory.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

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
