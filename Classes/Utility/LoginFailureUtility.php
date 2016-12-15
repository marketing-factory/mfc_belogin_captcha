<?php
namespace Mfc\MfcBeloginCaptcha\Utility;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 Sebastian Fischer <typo3@marketing-factory.de>
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
 * Class LoginFailureCountViewHelper
 *
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
    public static function failuresEqual($amount)
    {
        $amount = (int) $amount;

        if (!isset(static::$register[$amount])) {
            $table = 'sys_log';
            $ip = static::getDatabaseConnection()->fullQuoteStr(
                \TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('REMOTE_ADDR'),
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
     * @return \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected static function getDatabaseConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }
}
