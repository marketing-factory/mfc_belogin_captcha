<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Backend\Controller\LoginController::class] = [
    'className' => \Mfc\MfcBeloginCaptcha\Controller\LoginController::class
];

/** @noinspection PhpUndefinedVariableInspection */
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addService(
    $_EXTKEY, 'auth', 'Mfc\\MfcBeloginCaptcha\\Service\\CaptchaService',
    [
        'title' => 'Backend Login Captcha Service',
        'description' => 'Extends the authentication with a captcha protection after an amount of login failed',
        'subtype' => 'authUserBE',
        'available' => true,
        'priority' => 70,
        'quality' => 75,
        'os' => '',
        'exec' => '',
        'className' => 'Mfc\\MfcBeloginCaptcha\\Service\\CaptchaService',
    ]
);


?>