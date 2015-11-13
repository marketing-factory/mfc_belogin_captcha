<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

if ((class_exists('t3lib_utility_VersionNumber') &&
        t3lib_utility_VersionNumber::convertVersionNumberToInteger(TYPO3_version) >= '6000000') ||
    t3lib_div::int_from_ver(TYPO3_version) >= '6000000'
) {
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
} else {
    // lower 6.0 related stuff remove after 4.7 ceased
    $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['typo3/index.php'] =
        t3lib_extMgm::extPath('mfc_belogin_captcha') . 'Classes/Xclass/BackendLogin.php';
    $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['t3lib/class.t3lib_beuserauth.php'] =
        t3lib_extMgm::extPath('mfc_belogin_captcha') . 'Classes/Xclass/BeUserAuth.php';

    /** @noinspection PhpUndefinedVariableInspection */
    t3lib_extMgm::addService(
        $_EXTKEY, 'auth', 'Tx_MfcBeloginCaptcha_CaptchaService',
        [
            'title' => 'Backend Login Captcha Service',
            'description' => 'Extends the authentication with a captcha protection after an amount of login failed',
            'subtype' => 'authUserBE',
            'available' => true,
            'priority' => 55,
            'quality' => 55,
            'os' => '',
            'exec' => '',
            'classFile' => t3lib_extMgm::extPath($_EXTKEY) . 'Classes/Service/CaptchaService.4x.php',
            'className' => 'Tx_MfcBeloginCaptcha_CaptchaService',
        ]
    );
}

?>