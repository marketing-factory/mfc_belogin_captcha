<?php

$extensionPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('mfc_belogin_captcha');

return [
    'tx_mfcbelogincaptcha_captchaservice' => $extensionPath . '/Classes/Service/CaptchaService.4x.php',
    'tx_mfcbelogincaptcha_settingsservice' => $extensionPath . '/Classes/Service/SettingsService.4x.php',

    'ux_sc_index' => $extensionPath . '/Classes/XClass/BackendLogin.php',
    'ux_t3lib_beuserauth' => $extensionPath . '/Classes/XClass/BeUserAuth.php',
]

?>