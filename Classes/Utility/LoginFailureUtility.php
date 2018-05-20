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

use TYPO3\CMS\Core\Database\Connection;

class LoginFailureUtility
{
    /**
     * @var array
     */
    protected static $register;

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

            $queryBuilder = self::getQueryBuilderForTable($table);
            $queryBuilder->getRestrictions()->removeAll();
            $expression = $queryBuilder->expr();
            $rows = $queryBuilder
                ->select('error')
                ->from($table)
                ->where(
                    $expression->eq('type', $queryBuilder->createNamedParameter(255, \PDO::PARAM_INT)),
                    $expression->in(
                        'details_nr',
                        $queryBuilder->createNamedParameter([1,2], Connection::PARAM_INT_ARRAY)
                    ),
                    $expression->eq(
                        'IP',
                        $queryBuilder->createNamedParameter(
                            \TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('REMOTE_ADDR'),
                            \PDO::PARAM_STR
                        )
                    ),
                    $expression->gt('tstamp', $queryBuilder->createNamedParameter(time() - 86400, \PDO::PARAM_INT))
                )
                ->orderBy('tstamp', \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING)
                ->setMaxResults($amount)
                ->execute()
                ->fetchAll();

            // filter away all non errors
            $rows = array_filter($rows, function ($row) {
                return $row['error'] == 3 ? $row : '';
            });

            // compare remaining count with required amount
            static::$register[$amount] = count($rows) == $amount;
        }

        return static::$register[$amount];
    }

    /**
     * @param string $table
     *
     * @return \TYPO3\CMS\Core\Database\Query\QueryBuilder
     */
    protected static function getQueryBuilderForTable($table): \TYPO3\CMS\Core\Database\Query\QueryBuilder
    {
        return \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            \TYPO3\CMS\Core\Database\ConnectionPool::class
        )->getQueryBuilderForTable($table);
    }
}
