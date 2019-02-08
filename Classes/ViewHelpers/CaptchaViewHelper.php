<?php
namespace Mfc\MfcBeloginCaptcha\ViewHelpers;

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

use Mfc\MfcBeloginCaptcha\Service\SettingsService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class CaptchaViewHelper extends \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper
{
    /**
     * @var SettingsService
     */
    public $settingsService = null;

    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * CaptchaViewHelper constructor.
     */
    public function __construct()
    {
        parent::__construct();
        if (! ($this->objectManager instanceof \TYPO3\CMS\Extbase\Object\ObjectManagerInterface)) {
            $this->objectManager  = GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);
        }
        $this->settingsService = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(SettingsService::class);
    }

    /**
     * @return string
     */
    public function render()
    {
        // do not render captcha if settings are not set
        if (!$this->settingsService->getByPath('public_key')
            || !$this->settingsService->getByPath('private_key')
        ) {
            return '';
        }

        $this->settingsService->prepareRecaptchaSettings();

        $captchaService = \Evoweb\Recaptcha\Services\CaptchaService::getInstance();

        $this->tag->addAttributes([
            'class' => 'g-recaptcha',
            'data-sitekey' => $captchaService->getReCaptcha(),
            'style' => 'overflow: hidden; margin: 9px 0; width: 304px;'
        ]);
        $this->tag->forceClosingTag(true);

        return $this->tag->render();
    }
}
