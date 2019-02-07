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

class CaptchaViewHelper extends \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper
{
    /**
     * @return string
     */
    public function render()
    {
        $captchaService = \Evoweb\Recaptcha\Services\CaptchaService::getInstance();

        $this->tag->addAttributes([
            'class' => 'g-recaptcha',
            'data-sitekey' => $captchaService->getReCaptcha()
        ]);
        $this->tag->forceClosingTag(true);

        return $this->tag->render();
    }
}
