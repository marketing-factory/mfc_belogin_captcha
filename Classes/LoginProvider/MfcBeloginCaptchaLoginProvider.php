<?php
namespace Mfc\MfcBeloginCaptcha\LoginProvider;

use TYPO3\CMS\Backend\Controller\LoginController;
use TYPO3\CMS\Backend\LoginProvider\UsernamePasswordLoginProvider;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Class MfcBeloginCaptchaLoginProvider
 * @package Mfc\MfcBeloginCaptcha\LoginProvider
 */
class MfcBeloginCaptchaLoginProvider extends UsernamePasswordLoginProvider
{
    /**
     * @param StandaloneView $view
     * @param PageRenderer $pageRenderer
     * @param LoginController $loginController
     */
    public function render(StandaloneView $view, PageRenderer $pageRenderer, LoginController $loginController) {
        parent::render($view, $pageRenderer, $loginController);

        $view->setTemplatePathAndFilename(
            GeneralUtility::getFileAbsFileName(
                'EXT:mfc_belogin_captcha/Resources/Private/Templates/Login.html'
            )
        );
    }

}
