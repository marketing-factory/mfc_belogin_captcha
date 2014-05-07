<?php
namespace Mfc\MfcBeloginCaptcha\Service;
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

/**
 * Class CaptchaService
 *
 * @package Mfc\MfcBeloginCaptcha\Service
 */
class CaptchaService extends \TYPO3\CMS\Sv\AbstractAuthenticationService {
	/**
	 * Settings Service
	 *
	 * @var \Mfc\MfcBeloginCaptcha\Service\SettingsService
	 * @inject
	 */
	protected $settingsService;

	public function __construct() {
		$this->settingsService = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Mfc\\MfcBeloginCaptcha\\Service\\SettingsService');
	}

	/**
	 * Method adds a further authUser method.
	 *
	 * Will return one of following authentication status codes:
	 * - 0 - captcha failed
	 * - 100 - just go on. User is not authenticated but there is still no reason to stop
	 *
	 * @return integer Authentication statuscode, one of 0 or 100
	 */
	public function authUser() {
		$result = 100;

		if ($this->loginFailureCountGreater($this->settingsService->getByPath('failedTries'))) {
				// read out challenge, answer and remote_addr
			$data = array(
				'remoteip' => $_SERVER['REMOTE_ADDR'],
				'challenge' => trim(\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('recaptcha_challenge_field')),
				'response' => trim(\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('recaptcha_response_field')),
				'privatekey' => $this->settingsService->getByPath('private_key'),
			);

				// first discard useless input
			if (empty($data['challenge']) || empty($data['response'])) {
				$result = 0;
				$GLOBALS['T3_VAR']['recaptcha_error'] = 'empty';
			} else {
				$response = $this->queryVerificationServer($data);
				if (!$response || strtolower($response[0]) == 'false') {
					$result = 0;
					$GLOBALS['T3_VAR']['recaptcha_error'] = $response[1];
				}
			}
		}

		return $result;
	}

	/**
	 * Query reCAPTCHA server for captcha-verification
	 *
	 * @param array $data
	 * @return array Array with verified- (boolean) and error-code (string)
	 */
	protected function queryVerificationServer($data) {
			// find first occurence of '//' inside server string
		$verifyServerInfo = @parse_url($this->settingsService->getByPath('verify_server'));

		if (empty($verifyServerInfo)) {
			$response = array(FALSE, 'recaptcha-not-reachable');
		} else {
			$paramStr = \TYPO3\CMS\Core\Utility\GeneralUtility::implodeArrayForUrl('', $data);
			$response = \TYPO3\CMS\Core\Utility\GeneralUtility::getURL($this->settingsService->getByPath('verify_server') . '?' . $paramStr);
			$response = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(LF, $response);
		}

		return $response;
	}

	/**
	 * Proof if login fails greater than amount
	 *
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
			'type = 255 AND details_nr in (1,2) AND IP = \'' . $database->quoteStr($ip, 'sys_log') . '\'',
			'',
			'tstamp DESC',
			$amount
		);

			// make sure all rows contain a login failure
		$rows = array_filter($rows, function ($row) { return $row['error'] == 3 ? $row : ''; });

		return count($rows) == $amount;
	}
}

?>