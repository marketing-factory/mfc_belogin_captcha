TYPO3 Extension `mfc_belogin_captcha` (`mfc/mfc-belogin-captcha`)
=======================================

[![Latest Stable Version](https://poser.pugx.org/mfc/mfc-belogin-captcha/v/stable)](https://packagist.org/packages/mfc/mfc-belogin-captcha)
[![License](https://poser.pugx.org/mfc/mfc-belogin-captcha/license)](https://packagist.org/packages/mfc/mfc-belogin-captcha)

This extension adds a configurable captcha to the backend login after a give amount of failed login tries.


## 1. Features

- Configurable captcha for backend login

## 2. Usage

### 1) Installation

#### Composer installations

For TYPO3 v10
```
composer require mfc/mfc-belogin-captcha ^5.0.0


For TYPO3 v9
```
composer require mfc/mfc-belogin-captcha ^4.1.0
```

### 3) Configure the extension

The extensions needs to be configured in the admin tools extensions.

| Name | Description | Default |
| ---- | ----------- | --------|
| failedTries | Failed logins before captcha gets rendered | 5 |
| api_server | reCAPTCHA API-server address | https://www.google.com/recaptcha/api.js |
| verify_server | reCAPTCHA VERIFY-server address | https://www.google.com/recaptcha/api/siteverify |
| public_key | reCAPTCHA public key ||
| private_key | reCAPTCHA private key ||
| lang | reCAPTCHA language ||

If no public_key and private_key are set, the configuration of the extension "recaptcha" will be used automatically. When there is no public_key and private_key configured in both extensions, no captcha will be displayed.

## 3. License

mfc/mfc-belogin-captcha is released under the terms of the [MIT License](LICENSE.md).

[1]: https://getcomposer.org/
