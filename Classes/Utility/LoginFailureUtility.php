<?php
namespace Mfc\MfcBeloginCaptcha\Utility;

/**
 * This file is developed by Marketing Factory Consulting GmbH.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

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
            $ip = \TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('REMOTE_ADDR');

            $queryBuilder = self::getQueryBuilderForTable('sys_log');
            $rows = $queryBuilder
                ->select('error')
                ->from('sys_log')
                ->where(
                    $queryBuilder->expr()->eq('type', 255),
                    $queryBuilder->expr()->in(
                        'details_nr',
                        $queryBuilder->createNamedParameter([1, 2], \Doctrine\DBAL\Connection::PARAM_STR_ARRAY)
                    ),
                    $queryBuilder->expr()->eq('IP', $queryBuilder->createNamedParameter($ip, \PDO::PARAM_STR))
                )
                ->orderBy('tstamp', 'DESC')
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

    protected static function getQueryBuilderForTable(string $table): \TYPO3\CMS\Core\Database\Query\QueryBuilder
    {
        /** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager */
        $objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            \TYPO3\CMS\Extbase\Object\ObjectManager::class
        );
        /** @var \TYPO3\CMS\Core\Database\Query\QueryBuilder $queryBuilder */
        $queryBuilder = $objectManager
            ->get(\TYPO3\CMS\Core\Database\ConnectionPool::class)
            ->getQueryBuilderForTable($table);

        return $queryBuilder;
    }
}
