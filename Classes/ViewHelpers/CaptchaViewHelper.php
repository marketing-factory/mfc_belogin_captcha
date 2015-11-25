<?php
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
namespace Mfc\MfcBeloginCaptcha\ViewHelpers;

use Evoweb\Recaptcha\Services\CaptchaService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

/**
 * Class CaptchaViewHelper
 * @package Mfc\MfcBeloginCaptcha\ViewHelpers
 */
class CaptchaViewHelper extends AbstractTagBasedViewHelper
{

    /**
     * @return string
     */
    public function render()
    {
        $captchaService = GeneralUtility::makeInstance(CaptchaService::class);

        $this->tag->addAttributes([
            'class' => 'g-recaptcha',
            'data-sitekey' => $captchaService->getReCaptcha()
        ]);
        $this->tag->forceClosingTag(true);

        return $this->tag->render();
    }

}
