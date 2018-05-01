<?php
namespace Mfc\MfcBeloginCaptcha\LoginProvider;

use TYPO3\CMS\Backend\LoginProvider\UsernamePasswordLoginProvider;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class MfcBeloginCaptchaLoginProvider extends UsernamePasswordLoginProvider
{
    /**
     * @param \TYPO3\CMS\Fluid\View\StandaloneView $view
     * @param \TYPO3\CMS\Core\Page\PageRenderer $pageRenderer
     * @param \TYPO3\CMS\Backend\Controller\LoginController $loginController
     */
    public function render(
        \TYPO3\CMS\Fluid\View\StandaloneView $view,
        \TYPO3\CMS\Core\Page\PageRenderer $pageRenderer,
        \TYPO3\CMS\Backend\Controller\LoginController $loginController
    ) {
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
