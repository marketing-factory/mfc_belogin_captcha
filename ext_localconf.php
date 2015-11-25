<?php
defined('TYPO3_MODE') or die('Access denied.');
/** @var string $_EXTKEY */

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addService(
    $_EXTKEY,
    'auth',
    \Mfc\MfcBeloginCaptcha\Service\CaptchaService::class,
    [
        'title' => 'Backend Login Captcha Service',
        'description' => 'Extends the authentication with a captcha protection after an amount of login failed',
        'subtype' => 'authUserBE',
        'available' => true,
        'priority' => 70,
        'quality' => 75,
        'os' => '',
        'exec' => '',
        'className' => \Mfc\MfcBeloginCaptcha\Service\CaptchaService::class,
    ]
);

/**
 * Here, the default login provider will be overridden
 * as we don't want to add a new kind of login but just
 * add a captcha to the username and password login.
 */
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['backend']['loginProviders'][1433416747]['provider']
    = Mfc\MfcBeloginCaptcha\LoginProvider\MfcBeloginCaptchaLoginProvider::class;
