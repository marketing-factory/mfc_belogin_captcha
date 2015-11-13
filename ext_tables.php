<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

if ((class_exists('t3lib_utility_VersionNumber') && t3lib_utility_VersionNumber::convertVersionNumberToInteger(TYPO3_version) >= '6000000') ||
    t3lib_div::int_from_ver(TYPO3_version) >= '6000000'
) {
    $GLOBALS['TBE_STYLES']['htmlTemplates']['EXT:backend/Resources/Private/Templates/login.html'] =
        'EXT:mfc_belogin_captcha/Resources/Private/Templates/Login/Login.html';

    /** @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher $signalSlotDispatcher */
    $signalSlotDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\SignalSlot\\Dispatcher');
    $signalSlotDispatcher->connect(
        'TYPO3\\CMS\\Backend\\Controller\\LoginController',
        \TYPO3\CMS\Backend\Controller\LoginController::SIGNAL_RenderLoginForm,
        'Mfc\\MfcBeloginCaptcha\\Slot\\LoginControllerSlot',
        \TYPO3\CMS\Backend\Controller\LoginController::SIGNAL_RenderLoginForm,
        false
    );
} else {
    // lower 6.0 related stuff remove after 4.7 ceased
    $GLOBALS['TBE_STYLES']['htmlTemplates']['templates/login.html'] =
        'EXT:mfc_belogin_captcha/Resources/Private/Templates/Login/Login.4x.html';

    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['typo3/index.php']['renderLoginForm']['mfc_belogin_captcha'] =
        'EXT:mfc_belogin_captcha/Classes/Hook/BackendLoginHook.php:Tx_MfcBeloginCaptcha_Hook_BackendLoginHook';
}

?>