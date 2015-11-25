<?php
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
namespace Mfc\MfcBeloginCaptcha\ViewHelpers;

use Mfc\MfcBeloginCaptcha\Service\SettingsService;
use Mfc\MfcBeloginCaptcha\Utility\LoginFailureUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractConditionViewHelper;

/**
 * Class TooManyAuthenticationFailures
 * @package Mfc\MfcBeloginCaptcha\ViewHelpers
 */
class IfTooManyAuthenticationFailuresViewHelper extends AbstractConditionViewHelper
{

    /**
     * @var SettingsService
     */
    static public $settingsService = null;

    /**
     * @param array $arguments
     *
     * @return bool
     */
    static protected function evaluateCondition($arguments = null)
    {
        if (static::$settingsService === null) {
            static::$settingsService = GeneralUtility::makeInstance(SettingsService::class);
        }

        return LoginFailureUtility::failuresEqual(
            static::$settingsService->getByPath('failedTries')
        );
    }

}
