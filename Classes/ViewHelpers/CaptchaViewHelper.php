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

use Evoweb\Recaptcha\Services\CaptchaService;

/**
 * Class CaptchaViewHelper
 *
 * @package Mfc\MfcBeloginCaptcha\ViewHelpers
 */
class CaptchaViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper
{
    /**
     * @return string
     */
    public function render()
    {
        $this->prepareSettingsForCaptchaRendering();

        $captchaService = $this->objectManager->get(CaptchaService::class);

        $this->tag->addAttributes([
            'class' => 'g-recaptcha',
            'data-sitekey' => $captchaService->getReCaptcha(),
            'style' => 'overflow: hidden; margin: 9px 0; width: 304px;'
        ]);
        $this->tag->forceClosingTag(true);

        return $this->tag->render();
    }

    /**
     * Filles extension settings of EXT:recaptcha with values of mfc_belogin_captcha
     */
    protected function prepareSettingsForCaptchaRendering()
    {
        $settingsService = $this->objectManager->get(\Mfc\MfcBeloginCaptcha\Service\SettingsService::class);

        $mfcBeloginCaptchaSettings = $settingsService->getSettings('mfc_belogin_captcha');
        $recaptchaSettings = array_merge($settingsService->getSettings('recaptcha'), $mfcBeloginCaptchaSettings);

        $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['recaptcha'] = serialize($recaptchaSettings);
    }
}
