<?php
namespace Mfc\MfcBeloginCaptcha\Service;

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

use Mfc\MfcBeloginCaptcha\Utility\LoginFailureUtility;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class CaptchaService
 *
 * @package Mfc\MfcBeloginCaptcha\Service
 */
class CaptchaService extends \TYPO3\CMS\Sv\AbstractAuthenticationService
{
    /**
     * User object
     *
     * @var BackendUserAuthentication
     */
    public $pObj;

    /**
     * Settings Service
     *
     * @var SettingsService
     */
    protected $settingsService;

    /**
     * @var \Evoweb\Recaptcha\Services\CaptchaService
     */
    protected $captchaService;

    /**
     * CaptchaService constructor.
     */
    public function __construct()
    {
        $this->settingsService = GeneralUtility::makeInstance(SettingsService::class);
        $this->captchaService = GeneralUtility::makeInstance(\Evoweb\Recaptcha\Services\CaptchaService::class);
    }

    /**
     * Method adds a further authUser method.
     *
     * Will return one of following authentication status codes:
     *  - 0 - captcha failed
     *  - 100 - just go on. User is not authenticated but there is still no reason to stop
     *
     * @return int Authentication statuscode, one of 0 or 100
     */
    public function authUser()
    {
        $statusCode = 100;

        if ($this->login['status'] != 'sudo-mode'
            && $this->settingsService->getByPath('public_key')
            && $this->settingsService->getByPath('private_key')
            && LoginFailureUtility::failuresEqual($this->settingsService->getByPath('failedTries'))
        ) {
            $result = $this->captchaService->validateReCaptcha();

            if (!$result['verified']) {
                $statusCode = 0;
                $this->pObj->writelog(
                    255,
                    3,
                    3,
                    3,
                    'Login-attempt from %s (%s) for %s, captcha was not accepted! (Result: %s ERROR: %s)',
                    [
                        $this->authInfo['REMOTE_ADDR'],
                        $this->authInfo['REMOTE_HOST'],
                        $this->login['uname'],
                        $result['success'],
                        $result['error'],
                    ]
                );
            }
        }

        return $statusCode;
    }
}
