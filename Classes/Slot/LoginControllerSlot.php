<?php
namespace Mfc\MfcBeloginCaptcha\Slot;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Sebastian Fischer <typo@marketing-factory.de>
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

class LoginControllerSlot {
	/**
	 * Settings Service
	 *
	 * @var \Mfc\MfcBeloginCaptcha\Service\SettingsService
	 * @inject
	 */
	protected $settingsService;

	/**
	 * Object manager
	 *
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
	 * @inject
	 */
	protected $objectManager;

	/**
	 * @var \TYPO3\CMS\Backend\Controller\LoginController
	 */
	protected $controller;

	/**
	 * @var \tx_jmrecaptcha
	 */
	protected $captcha = NULL;

	public function __construct() {
		if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('jm_recaptcha')) {
			/** @noinspection PhpIncludeInspection */
			require_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('jm_recaptcha') . 'class.tx_jmrecaptcha.php');
			$this->captcha = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('tx_jmrecaptcha');
		}
	}

	/**
	 * @param \TYPO3\CMS\Backend\Controller\LoginController $controller
	 * @param array $marker
	 * @return mixed
	 */
	public function renderLoginForm($controller, $marker) {
		$this->controller = $controller;
		$marker['CAPTCHA'] = '';

		if ($this->loginFailureCountGreater($this->settingsService->getByPath('failedTries'))) {
			$marker['CAPTCHA'] = $this->getReCaptcha();
		}

		return array($controller, $marker);
	}

	/**
	 * @param integer $amount
	 * @return boolean
	 */
	protected function loginFailureCountGreater($amount) {
		/** @var \TYPO3\CMS\Core\Database\DatabaseConnection $database */
		$database = & $GLOBALS['TYPO3_DB'];
		$ip = \TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('REMOTE_ADDR');

		$rows = $database->exec_SELECTgetRows(
			'error',
			'sys_log',
			'type = 255 AND details_nr = 1 AND IP = \'' . $database->quoteStr($ip, 'sys_log') . '\'',
			'',
			'tstamp DESC',
			$amount
		);

			// make sure all rows contain a login failure
		$rows = array_filter($rows, function ($row) { return $row['error'] == 3 ? $row : ''; });

		return count($rows) == $amount;
	}

	/**
	 * @return string
	 */
	protected function getReCaptcha() {
			// extract server url and public key
		if ($this->settingsService->getByPath('use_ssl') ||
				\TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('TYPO3_SSL') ||
				\TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('TYPO3_PORT') == 443) {
			$server = rtrim($this->settingsService->getByPath('api_server_secure'), '/');
		} else {
			$server = rtrim($this->settingsService->getByPath('api_server'), '/');
		}

		$key = $this->settingsService->getByPath('public_key');

			// were any errors given in the last query?
		$error = '';
		if ($GLOBALS['T3_VAR']['recaptcha_error']) {
			$error = '&error=' . $GLOBALS['T3_VAR']['recaptcha_error'];
		}

		$recaptchaOptions = $this->getRecaptchaOptions();

		$content = $this->renderCaptchaError();
		$content .= '<script type="text/javascript">var RecaptchaOptions = { ' . $recaptchaOptions . ' };</script>
			<script type="text/javascript" src="' . htmlspecialchars($server . '/challenge?k=' . $key . $error) . '"></script>';
		return $content;
	}

	/**
	 * @return string
	 */
	protected function renderCaptchaError() {
		$result = '';

		if (isset($GLOBALS['T3_VAR']['recaptcha_error'])) {
			/** @var \TYPO3\CMS\Lang\LanguageService $language */
			$language = $GLOBALS['LANG'];
			$language->includeLLFile('EXT:mfc_belogin_captcha/Resources/Private/Language/locallang.xlf');
			$marker['ERROR_LOGIN_TITLE'] = $language->getLL('labels.recaptcha.error-title', TRUE);
			$marker['ERROR_LOGIN_DESCRIPTION'] = $language->getLL('labels.recaptcha.error-' . $GLOBALS['T3_VAR']['recaptcha_error'], TRUE);

			$template = \TYPO3\CMS\Core\Html\HtmlParser::getSubpart($GLOBALS['TBE_TEMPLATE']->moduleTemplate, '###LOGIN_ERROR###');
			$result = \TYPO3\CMS\Core\Html\HtmlParser::substituteMarkerArray($template, $marker, '###|###');
		}

		return $result;
	}

	/**
	 * @return string
	 */
	protected function getRecaptchaOptions() {
		$recaptchaOptions = array();

			// Language detection
		$language = $this->settingsService->getByPath('lang');
		if (!empty($language)) {
				// language from extension configuration
			$recaptchaOptions['lang'] = self::jsQuote($language);
		} elseif (!empty($GLOBALS['LANG']->lang)) {
				// automatic language detection (TYPO3 settings)
			$recaptchaOptions['lang'] = self::jsQuote($GLOBALS['LANG']->lang);
		}

			// Theme
		if ($this->settingsService->getByPath('theme')) {
			$recaptchaOptions['theme'] = self::jsQuote($this->settingsService->getByPath('theme'));
		}

			// TabIndex
		if ($this->settingsService->getByPath('tabindex')) {
			$recaptchaOptions['tabindex'] = self::jsQuote($this->settingsService->getByPath('tabindex'));
		}

			// TabIndex
		if ($this->settingsService->getByPath('custom_theme_widget')) {
			$recaptchaOptions['custom_theme_widget'] = self::jsQuote($this->settingsService->getByPath('custom_theme_widget'));
		}

			// Build option string
		$recaptchaOptionsTmp = array();
		foreach ($recaptchaOptions as $optionKey => $optionValue) {
			$recaptchaOptionsTmp[] = $optionKey . ' : ' . $optionValue;
		}
		return implode(', ', $recaptchaOptionsTmp);
	}

	/**
	 * Quote js-param-value
	 *
	 * @param string $value Value
	 * @return string Quoted value
	 */
	protected static function jsQuote($value) {
		return '\'' . addslashes((string) $value) . '\'';
	}
}

?>