<?php

/**
 * The phpMyFAQ Google ReCAPTCHA v3 class.
 *
 * This Source Code Form is subject to the terms of the Mozilla Public License,
 * v. 2.0. If a copy of the MPL was not distributed with this file, You can
 * obtain one at https://mozilla.org/MPL/2.0/.
 *
 * @package   phpMyFAQ
 * @author    Thorsten Rinne <thorsten@phpmyfaq.de>
 * @copyright 2023 phpMyFAQ Team
 * @license   https://www.mozilla.org/MPL/2.0/ Mozilla Public License Version 2.0
 * @link      https://www.phpmyfaq.de
 * @since     2023-02-06
 */

namespace phpMyFAQ\Captcha;

use phpMyFAQ\Configuration;

class GoogleRecaptcha implements CaptchaInterface
{
    private string $sessionId;
    private bool $userIsLoggedIn;

    /**
     * Constructor.
     */
    public function __construct(private Configuration $config)
    {
    }

    public function checkCaptchaCode(string $code): bool
    {
        $url = sprintf(
            'https://www.google.com/recaptcha/api/siteverify?secret=%s&response=%s',
            $this->config->get('security.googleReCaptchaV2SecretKey'),
            $code
        );

        $response = file_get_contents($url);
        $response = json_decode($response, true);

        if ($response['success'] === true) {
            return true;
        }

        return false;
    }

    /**
     * Setter for session id.
     */
    public function setSessionId(string $sessionId): GoogleRecaptcha
    {
        $this->sessionId = $sessionId;
        return $this;
    }

    public function isUserIsLoggedIn(): bool
    {
        return $this->userIsLoggedIn;
    }

    public function setUserIsLoggedIn(bool $userIsLoggedIn): GoogleRecaptcha
    {
        $this->userIsLoggedIn = $userIsLoggedIn;
        return $this;
    }
}
