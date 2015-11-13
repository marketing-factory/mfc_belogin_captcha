<?php

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\HttpUtility;

class ux_t3lib_beUserAuth extends \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
{
    /**
     * Checks if a submission of username and password is present or use other authentication by auth services
     *
     * @throws RuntimeException
     * @return void
     * @internal
     */
    public function checkAuthentication()
    {

        // No user for now - will be searched by service below
        $tempuserArr = [];
        $tempuser = false;

        // User is not authenticated by default
        $authenticated = false;

        // User want to login with passed login data (name/password)
        $activeLogin = false;

        // Indicates if an active authentication failed (not auto login)
        $this->loginFailure = false;

        if ($this->writeDevLog) {
            GeneralUtility::devLog('Login type: ' . $this->loginType, 't3lib_userAuth');
        }

        // The info array provide additional information for auth services
        $authInfo = $this->getAuthInfoArray();

        // Get Login/Logout data submitted by a form or params
        $loginData = $this->getLoginFormData();

        if ($this->writeDevLog) {
            GeneralUtility::devLog('Login data: ' . GeneralUtility::arrayToLogString($loginData), 't3lib_userAuth');
        }


        // active logout (eg. with "logout" button)
        if ($loginData['status'] == 'logout') {
            if ($this->writeStdLog) {
                // $type,$action,$error,$details_nr,$details,$data,$tablename,$recuid,$recpid
                $this->writelog(255, 2, 0, 2, 'User %s logged out', [$this->user['username']], '', 0, 0);
            } // Logout written to log
            if ($this->writeDevLog) {
                GeneralUtility::devLog('User logged out. Id: ' . $this->id, 't3lib_userAuth', -1);
            }

            $this->logoff();
        }

        // active login (eg. with login form)
        if ($loginData['status'] == 'login') {
            $activeLogin = true;

            if ($this->writeDevLog) {
                GeneralUtility::devLog('Active login (eg. with login form)', 't3lib_userAuth');
            }

            // check referer for submitted login values
            if ($this->formfield_status && $loginData['uident'] && $loginData['uname']) {
                $httpHost = GeneralUtility::getIndpEnv('TYPO3_HOST_ONLY');
                if (!$this->getMethodEnabled && ($httpHost != $authInfo['refInfo']['host'] && !$GLOBALS['TYPO3_CONF_VARS']['SYS']['doNotCheckReferer'])) {
                    throw new RuntimeException(
                        'TYPO3 Fatal Error: Error: This host address ("' . $httpHost . '") and the referer host ("' . $authInfo['refInfo']['host'] . '") mismatches!<br />
						It\'s possible that the environment variable HTTP_REFERER is not passed to the script because of a proxy.<br />
						The site administrator can disable this check in the "All Configuration" section of the Install Tool (flag: TYPO3_CONF_VARS[SYS][doNotCheckReferer]).',
                        1270853930
                    );
                }

                // delete old user session if any
                $this->logoff();
            }

            // Refuse login for _CLI users, if not processing a CLI request type
            // (although we shouldn't be here in case of a CLI request type)
            if ((strtoupper(substr($loginData['uname'], 0,
                        5)) == '_CLI_') && !(TYPO3_REQUESTTYPE & TYPO3_REQUESTTYPE_CLI)
            ) {
                throw new RuntimeException(
                    'TYPO3 Fatal Error: You have tried to login using a CLI user. Access prohibited!',
                    1270853931
                );
            }
        }


        // the following code makes auto-login possible (if configured). No submitted data needed

        // determine whether we need to skip session update.
        // This is used mainly for checking session timeout without
        // refreshing the session itself while checking.
        if (GeneralUtility::_GP('skipSessionUpdate')) {
            $skipSessionUpdate = true;
        } else {
            $skipSessionUpdate = false;
        }

        // re-read user session
        $authInfo['userSession'] = $this->fetchUserSession($skipSessionUpdate);
        $haveSession = is_array($authInfo['userSession']) ? true : false;

        if ($this->writeDevLog) {
            if ($haveSession) {
                GeneralUtility::devLog('User session found: ' . GeneralUtility::arrayToLogString($authInfo['userSession'],
                        [$this->userid_column, $this->username_column]), 't3lib_userAuth', 0);
            }
            if (is_array($this->svConfig['setup'])) {
                GeneralUtility::devLog('SV setup: ' . GeneralUtility::arrayToLogString($this->svConfig['setup']),
                    't3lib_userAuth', 0);
            }
        }

        // fetch user if ...
        if ($activeLogin
            || (!$haveSession && $this->svConfig['setup'][$this->loginType . '_fetchUserIfNoSession'])
            || $this->svConfig['setup'][$this->loginType . '_alwaysFetchUser']
        ) {

            // use 'auth' service to find the user
            // first found user will be used
            $serviceChain = '';
            $subType = 'getUser' . $this->loginType;
            while (is_object($serviceObj = GeneralUtility::makeInstanceService('auth', $subType, $serviceChain))) {
                $serviceChain .= ',' . $serviceObj->getServiceKey();
                $serviceObj->initAuth($subType, $loginData, $authInfo, $this);
                if ($row = $serviceObj->getUser()) {
                    $tempuserArr[] = $row;

                    if ($this->writeDevLog) {
                        GeneralUtility::devLog('User found: ' . GeneralUtility::arrayToLogString($row,
                                [$this->userid_column, $this->username_column]), 't3lib_userAuth', 0);
                    }

                    // user found, just stop to search for more if not configured to go on
                    if (!$this->svConfig['setup'][$this->loginType . '_fetchAllUsers']) {
                        break;
                    }
                }
                unset($serviceObj);
            }
            unset($serviceObj);

            if ($this->writeDevLog && $this->svConfig['setup'][$this->loginType . '_alwaysFetchUser']) {
                GeneralUtility::devLog($this->loginType . '_alwaysFetchUser option is enabled', 't3lib_userAuth');
            }
            if ($this->writeDevLog && $serviceChain) {
                GeneralUtility::devLog($subType . ' auth services called: ' . $serviceChain, 't3lib_userAuth');
            }
            if ($this->writeDevLog && !count($tempuserArr)) {
                GeneralUtility::devLog('No user found by services', 't3lib_userAuth');
            }
            if ($this->writeDevLog && count($tempuserArr)) {
                GeneralUtility::devLog(count($tempuserArr) . ' user records found by services', 't3lib_userAuth');
            }
        }


        // If no new user was set we use the already found user session
        if (!count($tempuserArr) && $haveSession) {
            $tempuserArr[] = $authInfo['userSession'];
            $tempuser = $authInfo['userSession'];
            // User is authenticated because we found a user session
            $authenticated = true;

            if ($this->writeDevLog) {
                GeneralUtility::devLog('User session used: ' . GeneralUtility::arrayToLogString($authInfo['userSession'],
                        [$this->userid_column, $this->username_column]), 't3lib_userAuth');
            }
        }


        // Re-auth user when 'auth'-service option is set
        if ($this->svConfig['setup'][$this->loginType . '_alwaysAuthUser']) {
            $authenticated = false;
            if ($this->writeDevLog) {
                GeneralUtility::devLog('alwaysAuthUser option is enabled', 't3lib_userAuth');
            }
        }


        // Authenticate the user if needed
        if (count($tempuserArr) && !$authenticated) {

            foreach ($tempuserArr as $tempuser) {

                // use 'auth' service to authenticate the user
                // if one service returns FALSE then authentication failed
                // a service might return 100 which means there's no reason to stop but the user can't be authenticated by that service

                if ($this->writeDevLog) {
                    GeneralUtility::devLog('Auth user: ' . GeneralUtility::arrayToLogString($tempuser), 't3lib_userAuth');
                }

                $serviceChain = '';
                $subType = 'authUser' . $this->loginType;
                while (is_object($serviceObj = GeneralUtility::makeInstanceService('auth', $subType, $serviceChain))) {
                    $serviceChain .= ',' . $serviceObj->getServiceKey();
                    $serviceObj->initAuth($subType, $loginData, $authInfo, $this);
                    if (($ret = $serviceObj->authUser($tempuser)) > 0) {

                        // if the service returns >=200 then no more checking is needed - useful for IP checking without password
                        if (intval($ret) >= 200) {
                            $authenticated = true;
                            break;
                        } elseif (intval($ret) >= 100) {
                            // Just go on. User is still not authenticated but there's no reason to stop now.
                        } else {
                            $authenticated = true;
                        }

                    } else {
                        $authenticated = false;
                        break;
                    }
                    unset($serviceObj);
                }
                unset($serviceObj);

                if ($this->writeDevLog && $serviceChain) {
                    GeneralUtility::devLog($subType . ' auth services called: ' . $serviceChain, 't3lib_userAuth');
                }

                if ($authenticated) {
                    // leave foreach() because a user is authenticated
                    break;
                }
            }
        }

        // If user is authenticated a valid user is in $tempuser
        if ($authenticated) {
            // reset failure flag
            $this->loginFailure = false;

            // Insert session record if needed:
            if (!($haveSession && (
                    $tempuser['ses_id'] == $this->id || // check if the tempuser has the current session id
                    $tempuser['uid'] == $authInfo['userSession']['ses_userid'] // check if the tempuser has the uid of the fetched session user
                ))
            ) {
                $this->createUserSession($tempuser);

                // The login session is started.
                $this->loginSessionStarted = true;
            }

            // User logged in - write that to the log!
            if ($this->writeStdLog && $activeLogin) {
                $this->writelog(255, 1, 0, 1,
                    'User %s logged in from %s (%s)',
                    [
                        $tempuser[$this->username_column],
                        GeneralUtility::getIndpEnv('REMOTE_ADDR'),
                        GeneralUtility::getIndpEnv('REMOTE_HOST'),
                    ],
                    '', '', '', -1, '', $tempuser['uid']
                );
            }

            if ($this->writeDevLog && $activeLogin) {
                GeneralUtility::devLog('User ' . $tempuser[$this->username_column] . ' logged in from ' . GeneralUtility::getIndpEnv('REMOTE_ADDR') . ' (' . GeneralUtility::getIndpEnv('REMOTE_HOST') . ')',
                    't3lib_userAuth', -1);
            }
            if ($this->writeDevLog && !$activeLogin) {
                GeneralUtility::devLog('User ' . $tempuser[$this->username_column] . ' authenticated from ' . GeneralUtility::getIndpEnv('REMOTE_ADDR') . ' (' . GeneralUtility::getIndpEnv('REMOTE_HOST') . ')',
                    't3lib_userAuth', -1);
            }

            if ($GLOBALS['TYPO3_CONF_VARS']['BE']['lockSSL'] == 3 && $this->user_table == 'be_users') {
                $requestStr = substr(GeneralUtility::getIndpEnv('TYPO3_REQUEST_SCRIPT'),
                    strlen(GeneralUtility::getIndpEnv('TYPO3_SITE_URL') . TYPO3_mainDir));
                $backendScript = \TYPO3\CMS\Backend\Utility\BackendUtility::getBackendScript();
                if ($requestStr == $backendScript && GeneralUtility::getIndpEnv('TYPO3_SSL')) {
                    list(, $url) = explode('://', GeneralUtility::getIndpEnv('TYPO3_SITE_URL'), 2);
                    list($server, $address) = explode('/', $url, 2);
                    if (intval($GLOBALS['TYPO3_CONF_VARS']['BE']['lockSSLPort'])) {
                        $sslPortSuffix = ':' . intval($GLOBALS['TYPO3_CONF_VARS']['BE']['lockSSLPort']);
                        $server = str_replace($sslPortSuffix, '', $server); // strip port from server
                    }
                    HttpUtility::redirect('http://' . $server . '/' . $address . TYPO3_mainDir . $backendScript);
                }
            }

        } elseif ($activeLogin || count($tempuserArr)) {
            HttpUtility::setResponseCode(HttpUtility::HTTP_STATUS_401);
            $this->loginFailure = true;

            if ($this->writeDevLog && !count($tempuserArr) && $activeLogin) {
                GeneralUtility::devLog('Login failed: ' . GeneralUtility::arrayToLogString($loginData), 't3lib_userAuth', 2);
            }
            if ($this->writeDevLog && count($tempuserArr)) {
                GeneralUtility::devLog('Login failed: ' . GeneralUtility::arrayToLogString($tempuser,
                        [$this->userid_column, $this->username_column]), 't3lib_userAuth', 2);
            }
        }


        // If there were a login failure, check to see if a warning email should be sent:
        if ($this->loginFailure && $activeLogin) {
            if ($this->writeDevLog) {
                GeneralUtility::devLog('Call checkLogFailures: ' . GeneralUtility::arrayToLogString([
                        'warningEmail' => $this->warningEmail,
                        'warningPeriod' => $this->warningPeriod,
                        'warningMax' => $this->warningMax,
                    ]), 't3lib_userAuth', -1);
            }

            $this->checkLogFailures($this->warningEmail, $this->warningPeriod, $this->warningMax);
        }

        if ($activeLogin && !$tempuser) {
            $this->logUnknowUserLogin($loginData);
        }
    }

    /**
     * @param array $loginData
     * @return void
     */
    protected function logUnknowUserLogin($loginData)
    {
        $this->writelog(255, 3, 3, 1,
            'Login-attempt from %s (%s), username \'%s\', username unknown!',
            [GeneralUtility::getIndpEnv('REMOTE_ADDR'), GeneralUtility::getIndpEnv('REMOTE_HOST'), $loginData['uname']]
        );
    }
}

?>
