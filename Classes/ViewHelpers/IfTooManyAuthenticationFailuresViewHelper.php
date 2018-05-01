<?php
namespace Mfc\MfcBeloginCaptcha\ViewHelpers;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractConditionViewHelper;

class IfTooManyAuthenticationFailuresViewHelper extends AbstractConditionViewHelper
{
    /**
     * @var \Mfc\MfcBeloginCaptcha\Service\SettingsService
     */
    public static $settingsService = null;

    /**
     * @param array $arguments
     *
     * @return bool
     */
    protected static function evaluateCondition($arguments = null)
    {
        if (static::$settingsService === null) {
            static::$settingsService = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
                \Mfc\MfcBeloginCaptcha\Service\SettingsService::class
            );
        }

        return \Mfc\MfcBeloginCaptcha\Utility\LoginFailureUtility::failuresEqual(
            static::$settingsService->getByPath('failedTries')
        );
    }
}
