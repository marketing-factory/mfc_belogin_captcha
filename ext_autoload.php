<?php

$extensionPath = t3lib_extMgm::extPath('mfc_belogin_captcha');

return array(
	'tx_mfcbelogincaptcha_captchaservice' => $extensionPath . '/Classes/Service/CaptchaService.4x.php',
	'tx_mfcbelogincaptcha_settingsservice' => $extensionPath . '/Classes/Service/SettingsService.4x.php',

	'ux_sc_index' => $extensionPath . '/Classes/XClass/BackendLogin.php',
	'ux_t3lib_beuserauth' => $extensionPath . '/Classes/XClass/BeUserAuth.php',
)

?>