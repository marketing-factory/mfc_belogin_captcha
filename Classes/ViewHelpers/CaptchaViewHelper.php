<?php
namespace Mfc\MfcBeloginCaptcha\ViewHelpers;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

class CaptchaViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper
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
