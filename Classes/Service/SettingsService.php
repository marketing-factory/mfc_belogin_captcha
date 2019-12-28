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
    protected static $settings = [];

    /**
     * Returns all settings.
     *
     * @param string $extensionKey
     *
     * @return array
     */
    public function getSettings($extensionKey = 'mfc_belogin_captcha')
    {
        if (!isset(self::$settings[$extensionKey])) {
            if (class_exists(\TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class)) {
                self::$settings[$extensionKey] = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
                    \TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class
                )->get('mfc_belogin_captcha');
            } else {
                self::$settings[$extensionKey] = !is_array(
                    $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$extensionKey]
                ) ?
                    unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$extensionKey]) :
                    $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$extensionKey];
            }
        }

        return self::$settings[$extensionKey];
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
        $pathValue = \TYPO3\CMS\Extbase\Reflection\ObjectAccess::getPropertyPath($this->getSettings(), $path);

        if (!$pathValue) {
            $pathValue = \TYPO3\CMS\Extbase\Reflection\ObjectAccess::getPropertyPath($this->getSettings('recaptcha'), $path);
        }

        return $pathValue;
    }


    /**
     * Filles extension settings of EXT:recaptcha with values of mfc_belogin_captcha
     */
    public function prepareRecaptchaSettings()
    {
        $mfcBeloginCaptchaSettings = $this->getSettings('mfc_belogin_captcha');
        $recaptchaSettings = array_merge($this->getSettings('recaptcha'), $mfcBeloginCaptchaSettings);

        $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['recaptcha'] = serialize($recaptchaSettings);

        if (!isset($recaptchaSettings['public_key']) || empty($recaptchaSettings['public_key'])) {
            /** @var \TYPO3\CMS\Core\Log\Logger $logger */
            $logger = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
                \TYPO3\CMS\Core\Log\LogManager::class
            )->getLogger(__CLASS__);
            $logger->log(
                \TYPO3\CMS\Core\Log\LogLevel::WARNING,
                'Recaptcha public key was empty.',
                ['extension' => 'mfc_belogin_captcha']
            );
        }
    }
}