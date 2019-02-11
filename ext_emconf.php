<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "mfc_belogin_captcha".
 *
 * Auto generated 19-09-2013 09:58
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Backend Login Captcha',
	'description' => 'Add an configurable captcha to the backend login after a give amount of failed login tries',
	'category' => 'be',
	'author' => 'Sebastian Fischer',
	'author_email' => 'typo3@marketing-factory.de',
	'shy' => '',
	'conflicts' => '',
	'priority' => 'bottom',
	'module' => '',
	'state' => 'beta',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 1,
	'lockType' => '',
	'author_company' => 'Marketing Factory Consulting GmbH',
	'version' => '1.2.1',
	'constraints' => array(
		'depends' => array(
			'typo3' => '4.5.0-6.2.99',
			'php' => '5.3.0-0.0.0',
			'extbase' => '1.3.4-0.0.0'
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'suggests' => array(
	),
	'_md5_values_when_last_written' => 'a:17:{s:16:"ext_autoload.php";s:4:"2711";s:21:"ext_conf_template.txt";s:4:"c0c8";s:12:"ext_icon.gif";s:4:"3527";s:17:"ext_localconf.php";s:4:"8f3f";s:14:"ext_tables.php";s:4:"69a6";s:33:"Classes/Hook/BackendLoginHook.php";s:4:"5290";s:37:"Classes/Service/CaptchaService.4x.php";s:4:"6ce1";s:34:"Classes/Service/CaptchaService.php";s:4:"8bb2";s:38:"Classes/Service/SettingsService.4x.php";s:4:"fc61";s:35:"Classes/Service/SettingsService.php";s:4:"d7dd";s:36:"Classes/Slot/LoginControllerSlot.php";s:4:"7710";s:31:"Classes/Xclass/BackendLogin.php";s:4:"d6c2";s:29:"Classes/Xclass/BeUserAuth.php";s:4:"f3fa";s:40:"Resources/Private/Language/locallang.xlf";s:4:"fd9a";s:40:"Resources/Private/Language/locallang.xml";s:4:"6d72";s:47:"Resources/Private/Templates/Login/Login.4x.html";s:4:"6c22";s:44:"Resources/Private/Templates/Login/Login.html";s:4:"3428";}',
);

?>
