<?php

class Tx_MfcBeloginCaptcha_Hook_BackendLoginHook {
	/**
	 * Settings Service
	 *
	 * @var Tx_MfcBeloginCaptcha_SettingsService4x
	 */
	protected $settingsService;

	/**
	 * @var ux_SC_index
	 */
	protected $controller;

	/**
	 * @var \tx_jmrecaptcha
	 */
	protected $captcha = NULL;

	public function __construct() {
		$this->settingsService = t3lib_div::makeInstance('Tx_MfcBeloginCaptcha_SettingsService');
	}

	/**
	 * @param ux_SC_index $controller
	 * @param array $marker
	 * @return mixed
	 */
	public function renderLoginForm($controller, $marker) {
		$this->controller = $controller;
		$marker['CAPTCHA'] = '';

		if ($this->loginFailureCountGreater($this->settingsService->getByPath('failedTries'))) {
			$marker['CAPTCHA'] = '<div style="margin-top: 5px">' . $this->getReCaptcha() . '</div>';
		}

		return array($controller, $marker);
	}

	/**
	 * @param integer $amount
	 * @return boolean
	 */
	protected function loginFailureCountGreater($amount) {
		/** @var t3lib_db $database */
		$database = & $GLOBALS['TYPO3_DB'];
		$ip = t3lib_div::getIndpEnv('REMOTE_ADDR');

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
				t3lib_div::getIndpEnv('TYPO3_SSL') ||
				t3lib_div::getIndpEnv('TYPO3_PORT') == 443) {
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
			/** @var language $language */
			$language = $GLOBALS['LANG'];
			$language->includeLLFile('EXT:mfc_belogin_captcha/Resources/Private/Language/locallang.xml');
			$marker['ERROR_LOGIN_TITLE'] = $language->getLL('labels.recaptcha.error-title', TRUE);
			$marker['ERROR_LOGIN_DESCRIPTION'] = $language->getLL('labels.recaptcha.error-' . $GLOBALS['T3_VAR']['recaptcha_error'], TRUE);

			$template = t3lib_parsehtml::getSubpart($GLOBALS['TBE_TEMPLATE']->moduleTemplate, '###LOGIN_ERROR###');
			$result = t3lib_parsehtml::substituteMarkerArray($template, $marker, '###|###');
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