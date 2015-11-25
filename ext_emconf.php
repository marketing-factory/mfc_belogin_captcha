<?php
/** @var string $_EXTKEY */

$EM_CONF[$_EXTKEY] = [
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
    'version' => '2.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '7.6.0-7.6.99',
            'php' => '5.5.0-5.6.99',
            'recaptcha' => ''
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],
    'suggests' => [
    ],
    '_md5_values_when_last_written' => '',
];
