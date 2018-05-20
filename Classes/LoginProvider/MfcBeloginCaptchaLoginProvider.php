<?php
namespace Mfc\MfcBeloginCaptcha\LoginProvider;

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

use TYPO3\CMS\Backend\Controller\LoginController;
use TYPO3\CMS\Backend\LoginProvider\UsernamePasswordLoginProvider;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Class MfcBeloginCaptchaLoginProvider
 *
 * @package Mfc\MfcBeloginCaptcha\LoginProvider
 */
class MfcBeloginCaptchaLoginProvider extends \TYPO3\CMS\Backend\LoginProvider\UsernamePasswordLoginProvider
{
    /**
     * Override render to set a different login template
     *
     * @param StandaloneView $view
     * @param PageRenderer $pageRenderer
     * @param LoginController $loginController
     *
     * @return void
     */
    public function render(StandaloneView $view, PageRenderer $pageRenderer, LoginController $loginController)
    {
        parent::render($view, $pageRenderer, $loginController);

        $useInvisible = (bool)GeneralUtility::makeInstance(
            \Mfc\MfcBeloginCaptcha\Service\SettingsService::class
        )->getByPath('useInvisible');

        if ($useInvisible) {
            if (version_compare(TYPO3_version, '9.0.0', '<')) {
                $folder = 'Layouts8/';
            } else {
                $folder = 'Layouts/';
            }

            $view->setLayoutRootPaths(array_merge(
                $view->getLayoutRootPaths(),
                $layoutPathes = [GeneralUtility::getFileAbsFileName(
                    'EXT:mfc_belogin_captcha/Resources/Private/' . $folder
                )]
            ));

            $captchaService = \Evoweb\Recaptcha\Services\CaptchaService::getInstance();
            $view->assign('sitekey', $captchaService->getReCaptcha());
        } else {
            $view->setTemplatePathAndFilename(
                GeneralUtility::getFileAbsFileName(
                    'EXT:mfc_belogin_captcha/Resources/Private/Templates/UserPassLoginForm.html'
                )
            );
        }
    }
}
