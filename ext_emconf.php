<?php

$EM_CONF['mfc_belogin_captcha'] = [
    'title' => 'Backend Login Captcha',
    'description' => 'Add an configurable captcha to the backend login after a give amount of failed login tries',
    'version' => '5.0.1',
    'author' => 'Simon Schmidt, Christian Hellmund, Alexander Schnitzler',
    'author_email' => 'typo3@marketing-factory.de',
    'author_company' => 'Marketing Factory Consulting GmbH',
    'category' => 'be',
    'priority' => 'bottom',
    'state' => 'stable',
    'clearCacheOnLoad' => 1,
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.0-10.4.99',
            'recaptcha' => '10.0.0-10.99.99'
        ],
    ],
];
