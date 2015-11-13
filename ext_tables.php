<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

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

?>