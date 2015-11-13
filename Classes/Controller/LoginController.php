<?php
namespace Mfc\MfcBeloginCaptcha\Controller;

use TYPO3\CMS\Core\Service\MarkerBasedTemplateService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class LoginController extends \TYPO3\CMS\Backend\Controller\LoginController
{
    /**
     * Wrapping the login form table in another set of tables etc:
     *
     * @param string $content HTML content for the login form
     * @return string The HTML for the page.
     */
    public function wrapLoginForm($content)
    {
        /** @var MarkerBasedTemplateService $templateService */
        $templateService = GeneralUtility::makeInstance(MarkerBasedTemplateService::class);

        $mainContent = $templateService->getSubpart($GLOBALS['TBE_TEMPLATE']->moduleTemplate, '###PAGE###');

        if ($GLOBALS['TBE_STYLES']['logo_login']) {
            $logo = '<img src="' . htmlspecialchars($GLOBALS['BACK_PATH'] . $GLOBALS['TBE_STYLES']['logo_login']) . '" alt="" />';
        } else {
            $logo = '<img' . t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/typo3logo.gif',
                    'width="123" height="34"') . ' alt="" />';
        }

        $markers = [
            'LOGO' => $logo,
            'LOGINBOX_IMAGE' => $this->makeLoginBoxImage(),
            'FORM' => $content,
            'NEWS' => $this->makeLoginNews(),
            'COPYRIGHT' => $this->makeCopyrightNotice(),
            'CSS_ERRORCLASS' => ($this->isLoginInProgress() ? ' class="error"' : ''),
            'CSS_OPENIDCLASS' => 't3-login-openid-' . (t3lib_extMgm::isLoaded('openid') ? 'enabled' : 'disabled'),

            // the labels will be replaced later on, thus the other parts above
            // can use these markers as well and it will be replaced
            'HEADLINE' => $GLOBALS['LANG']->getLL('headline', true),
            'INFO_ABOUT' => $GLOBALS['LANG']->getLL('info.about', true),
            'INFO_RELOAD' => $GLOBALS['LANG']->getLL('info.reset', true),
            'INFO' => $GLOBALS['LANG']->getLL('info.cookies_and_js', true),
            'ERROR_JAVASCRIPT' => $GLOBALS['LANG']->getLL('error.javascript', true),
            'ERROR_COOKIES' => $GLOBALS['LANG']->getLL('error.cookies', true),
            'ERROR_COOKIES_IGNORE' => $GLOBALS['LANG']->getLL('error.cookies_ignore', true),
            'ERROR_CAPSLOCK' => $GLOBALS['LANG']->getLL('error.capslock', true),
            'ERROR_FURTHERHELP' => $GLOBALS['LANG']->getLL('error.furtherInformation', true),
            'LABEL_DONATELINK' => $GLOBALS['LANG']->getLL('labels.donate', true),
            'LABEL_USERNAME' => $GLOBALS['LANG']->getLL('labels.username', true),
            'LABEL_OPENID' => $GLOBALS['LANG']->getLL('labels.openId', true),
            'LABEL_PASSWORD' => $GLOBALS['LANG']->getLL('labels.password', true),
            'LABEL_WHATISOPENID' => $GLOBALS['LANG']->getLL('labels.whatIsOpenId', true),
            'LABEL_SWITCHOPENID' => $GLOBALS['LANG']->getLL('labels.switchToOpenId', true),
            'LABEL_SWITCHDEFAULT' => $GLOBALS['LANG']->getLL('labels.switchToDefault', true),
            'CLEAR' => $GLOBALS['LANG']->getLL('clear', true),
            'LOGIN_PROCESS' => $GLOBALS['LANG']->getLL('login_process', true),
            'SITELINK' => '<a href="/">###SITENAME###</a>',

            // global variables will now be replaced (at last)
            'SITENAME' => htmlspecialchars($GLOBALS['TYPO3_CONF_VARS']['SYS']['sitename']),
        ];
        $this->emitRenderLoginFormHook($markers);
        return $templateService->substituteMarkerArray($mainContent, $markers, '###|###');
    }

    /**
     * @param array $markers
     * @return void
     */
    protected function emitRenderLoginFormHook(&$markers)
    {
        if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['typo3/index.php']['renderLoginForm'])) {
            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['typo3/index.php']['renderLoginForm'] as $classReference) {
                $hookObject = &GeneralUtility::getUserObj($classReference);
                if (method_exists($hookObject, 'renderLoginForm')) {
                    $parameter = $hookObject->renderLoginForm($this, $markers);
                    $markers = $parameter[1];
                }
            }
        }
    }
}

?>