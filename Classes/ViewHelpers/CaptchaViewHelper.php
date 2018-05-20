<?php
namespace Mfc\MfcBeloginCaptcha\ViewHelpers;

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

use Mfc\MfcBeloginCaptcha\Service\SettingsService;

/**
 * Class CaptchaViewHelper
 *
 * @package Mfc\MfcBeloginCaptcha\ViewHelpers
 */
class CaptchaViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper
{
    /**
     * @var SettingsService
     */
    public $settingsService = null;

    /**
     * CaptchaViewHelper constructor.
     */
    public function __construct()
    {
        parent::__construct();

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
