<?php
/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */
namespace Mfc\MfcBeloginCaptcha\Utility;

use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class LoginFailureCountViewHelper
 * @package Mfc\MfcBeloginCaptcha\ViewHelpers
 */
class LoginFailureUtility
{

    /**
     * @var array
     */
    static protected $register;

    /**
     * @param int $amount
     *
     * @return bool
     */
    static public function failuresEqual($amount)
    {
        $amount = (int) $amount;

        if (!isset(static::$register[$amount])) {
            $table = 'sys_log';
            $ip = static::getDatabaseConnection()->fullQuoteStr(
                GeneralUtility::getIndpEnv('REMOTE_ADDR'),
                $table
            );

            $rows = static::getDatabaseConnection()->exec_SELECTgetRows(
                'error',
                $table,
                'type = 255 AND details_nr in (1,2) AND IP = ' . $ip,
                '',
                'tstamp DESC',
                $amount
            );

            $rows = array_filter($rows, function ($row) {
                return $row['error'] == 3 ? $row : '';
            });

            static::$register[$amount] = count($rows) == $amount;
        }

        return static::$register[$amount];
    }

    /**
     * @return DatabaseConnection
     */
    static protected function getDatabaseConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }

}
