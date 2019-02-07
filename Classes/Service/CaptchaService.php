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

use TYPO3\CMS\Core\Utility\GeneralUtility;

class CaptchaService extends \TYPO3\CMS\Sv\AbstractAuthenticationService
{
    /**
     * Settings Service
     *
     * @var \Mfc\MfcBeloginCaptcha\Service\SettingsService
     */
    protected $settingsService;

    /**
     * @var \Evoweb\Recaptcha\Services\CaptchaService
     */
    protected $captchaService;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->settingsService = GeneralUtility::makeInstance(\Mfc\MfcBeloginCaptcha\Service\SettingsService::class);
        $this->captchaService = GeneralUtility::makeInstance(\Evoweb\Recaptcha\Services\CaptchaService::class);
    }

    /**
     * Method adds a further authUser method.
     *
     * Will return one of following authentication status codes:
     * - 0 - captcha failed
     * - 100 - just go on. User is not authenticated but there is still no reason to stop
     *
     * @return int Authentication statuscode, one of 0 or 100
     */
    public function authUser()
    {
        $statusCode = 100;

        if (\Mfc\MfcBeloginCaptcha\Utility\LoginFailureUtility::failuresEqual(
            $this->settingsService->getByPath('failedTries')
        )) {
            $result = $this->captchaService->validateReCaptcha();

            if (!$result['verified']) {
                $statusCode = 0;
            }
        }

        return $statusCode;
    }
}
