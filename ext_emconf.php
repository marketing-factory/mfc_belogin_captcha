<?php

$EM_CONF['mfc_belogin_captcha'] = [
    'title' => 'Backend Login Captcha',
    'description' => 'Add an configurable captcha to the backend login after a give amount of failed login tries',
    'category' => 'be',
    'version' => '4.0.0',
    'author' => 'Sebastian Fischer',
    'author_email' => 'typo3@marketing-factory.de',
    'author_company' => 'Marketing Factory Consulting GmbH',
    'priority' => 'bottom',
    'state' => 'stable',
    'clearCacheOnLoad' => 1,
    'constraints' => [
        'depends' => [
        'php' => '7.0.0-0.0.0',
        'typo3' => '9.4.0-9.5.99',
        'recaptcha' => ''
        ],
    ],
];
