<?php
namespace Mfc\MfcBeloginCaptcha\ViewHelpers;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 Sebastian Fischer <typo3@marketing-factory.de>
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

use Mfc\MfcBeloginCaptcha\Service\SettingsService;

class IfTooManyAuthenticationFailuresViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractConditionViewHelper
{
    /**
     * @var SettingsService
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
            static::$settingsService = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(SettingsService::class);
        }

        return \Mfc\MfcBeloginCaptcha\Utility\LoginFailureUtility::failuresEqual(
            static::$settingsService->getByPath('failedTries')
        );
    }
}
