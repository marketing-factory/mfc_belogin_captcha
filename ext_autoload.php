<?php

$extensionPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('mfc_belogin_captcha');

return [
    'ux_sc_index' => $extensionPath . '/Classes/XClass/BackendLogin.php',
    'ux_t3lib_beuserauth' => $extensionPath . '/Classes/XClass/BeUserAuth.php',
]

?>