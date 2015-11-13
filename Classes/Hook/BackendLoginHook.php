<?php
use TYPO3\CMS\Core\Service\MarkerBasedTemplateService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class Tx_MfcBeloginCaptcha_Hook_BackendLoginHook
 */
class Tx_MfcBeloginCaptcha_Hook_BackendLoginHook
{
    /**
     * Settings Service
     *
     * @var Tx_MfcBeloginCaptcha_SettingsService
     */
    protected $settingsService;

    /**
     * @var ux_SC_index
     */
    protected $controller;

    /**
     * @var \tx_jmrecaptcha
     */
    protected $captcha = null;

    /**
     * @var MarkerBasedTemplateService
     */
    protected $templateService;

    public function __construct()
    {
        $this->settingsService = GeneralUtility::makeInstance('Tx_MfcBeloginCaptcha_SettingsService');
        $this->templateService = GeneralUtility::makeInstance(MarkerBasedTemplateService::class);
    }

    /**
     * @param ux_SC_index $controller
     * @param array $marker
     * @return mixed
     */
    public function renderLoginForm($controller, $marker)
    {
        $this->controller = $controller;
        $marker['CAPTCHA'] = '';

        if ($this->loginFailureCountGreater($this->settingsService->getByPath('failedTries'))) {
            $marker['CAPTCHA'] = '<div style="margin-top: 5px">' . $this->getReCaptcha() . '</div>';
        }

        $marker['FORM'] = $this->renderCaptchaError($marker['FORM']);

        return [$controller, $marker];
    }

    /**
     * @param integer $amount
     * @return boolean
     */
    protected function loginFailureCountGreater($amount)
    {
        /** @var \TYPO3\CMS\Core\Database\DatabaseConnection $database */
        $database = &$GLOBALS['TYPO3_DB'];
        $ip = GeneralUtility::getIndpEnv('REMOTE_ADDR');

        $rows = $database->exec_SELECTgetRows(
            'error',
            'sys_log',
            'type = 255 AND details_nr = 1 AND IP = \'' . $database->quoteStr($ip, 'sys_log') . '\'',
            '',
            'tstamp DESC',
            $amount
        );

        // make sure all rows contain a login failure
        $rows = array_filter($rows, function ($row) {
            return $row['error'] == 3 ? $row : '';
        });

        return count($rows) == $amount;
    }

    /**
     * @return string
     */
    protected function getReCaptcha()
    {
        // extract server url and public key
        if ($this->isSslActive()) {
            $server = rtrim($this->settingsService->getByPath('api_server_secure'), '/');
        } else {
            $server = rtrim($this->settingsService->getByPath('api_server'), '/');
        }

        $key = $this->settingsService->getByPath('public_key');

        // were any errors given in the last query?
        $error = '';
        if ($GLOBALS['T3_VAR']['recaptcha_error']) {
            $error = '&error=' . $GLOBALS['T3_VAR']['recaptcha_error'];
        }

        $content = '<script type="text/javascript">var RecaptchaOptions = { ' . $this->getRecaptchaOptions() . ' };</script>';

        if ($this->settingsService->getByPath('theme') == 'custom') {
            $content .= $this->renderCustomCaptchaWidget($key);
        } else {
            $content .= '<script type="text/javascript" src="' . htmlspecialchars($server . '/challenge?k=' . $key . $error) . '"></script>';
        }

        return $content;
    }

    /**
     * @return boolean
     */
    protected function isSslActive()
    {
        return $this->settingsService->getByPath('use_ssl') ||
        GeneralUtility::getIndpEnv('TYPO3_SSL') ||
        GeneralUtility::getIndpEnv('TYPO3_PORT') == 443;
    }

    /**
     * @param string $form
     * @return string
     */
    protected function renderCaptchaError($form)
    {
        if (isset($GLOBALS['T3_VAR']['recaptcha_error'])) {
            /** @var \TYPO3\CMS\Lang\LanguageService $language */
            $language = $GLOBALS['LANG'];
            $language->includeLLFile('EXT:mfc_belogin_captcha/Resources/Private/Language/locallang.xml');
            $marker['ERROR_LOGIN_TITLE'] = $language->getLL('labels.recaptcha.error-title', true);
            $marker['ERROR_LOGIN_DESCRIPTION'] = $language->getLL('labels.recaptcha.error-' . $GLOBALS['T3_VAR']['recaptcha_error'],
                true);

            $template = $this->templateService->getSubpart($GLOBALS['TBE_TEMPLATE']->moduleTemplate, '###CAPTCHA_ERROR###');
            $result = $this->templateService->substituteMarkerArray($template, $marker, '###|###');

            $errors = $this->templateService->getSubpart($form, '###LOGIN_ERROR###');
            $errors = substr($errors, 0, strrpos($errors, '</div>')) . $result . '</div>';

            $form = $this->templateService->substituteSubpart($form, '###LOGIN_ERROR###', $errors);
        }

        $form = $this->templateService->substituteSubpart($form, '###CAPTCHA_ERROR###', '');

        return $form;
    }

    /**
     * @return string
     */
    protected function getRecaptchaOptions()
    {
        $recaptchaOptions = [];

        // Language detection
        $language = $this->settingsService->getByPath('lang');
        if (!empty($language)) {
            // language from extension configuration
            $recaptchaOptions['lang'] = self::jsQuote($language);
        } elseif (!empty($GLOBALS['LANG']->lang)) {
            // automatic language detection (TYPO3 settings)
            $recaptchaOptions['lang'] = self::jsQuote($GLOBALS['LANG']->lang);
        }

        // Theme
        if ($this->settingsService->getByPath('theme')) {
            $recaptchaOptions['theme'] = self::jsQuote($this->settingsService->getByPath('theme'));
        }

        // TabIndex
        if ($this->settingsService->getByPath('tabindex')) {
            $recaptchaOptions['tabindex'] = self::jsQuote($this->settingsService->getByPath('tabindex'));
        }

        // custom theme widget
        if ($this->settingsService->getByPath('custom_theme_widget')) {
            $recaptchaOptions['custom_theme_widget'] = self::jsQuote($this->settingsService->getByPath('custom_theme_widget'));
        }

        // Build option string
        $recaptchaOptionsTmp = [];
        foreach ($recaptchaOptions as $optionKey => $optionValue) {
            $recaptchaOptionsTmp[] = $optionKey . ' : ' . $optionValue;
        }
        return implode(', ', $recaptchaOptionsTmp);
    }

    /**
     * Quote js-param-value
     *
     * @param string $value Value
     * @return string Quoted value
     */
    protected static function jsQuote($value)
    {
        return '\'' . addslashes((string)$value) . '\'';
    }

    /**
     * @param string $key
     * @return string
     */
    protected function renderCustomCaptchaWidget($key)
    {
        $this->controller->content = str_replace(
            '/*###POSTCSSMARKER###*/',
            GeneralUtility::getUrl(GeneralUtility::getFileAbsFileName($this->settingsService->getByPath('widget_stylesheets'))) . LF . '/*###POSTCSSMARKER###*/',
            $this->controller->content
        );

        $template = GeneralUtility::getURL(GeneralUtility::getFileAbsFileName($this->settingsService->getByPath('widget_template')));

        $marker = [
            'key' => $key,
            'protocol' => $this->isSslActive() ? 'https' : 'http',
        ];

        return $this->templateService->substituteMarkerArray($template, $marker, '###|###', true);
    }
}

?>