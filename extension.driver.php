<?php

/**
 * Copyright (c) 2014 Thomas Appel and Michael Eichelsdoerfer
 * License: MIT
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */
require_once EXTENSIONS . '/anti_brute_force/extension.driver.php';

/**
 * Class: Extension_members_anti_brute_force
 *
 * @see Extension
 * @license MIT
 */
class Extension_members_anti_brute_force extends Extension
{
    /**
     * MembersDriver
     *
     * @var Extension_Members
     */
    protected $MembersDriver;

    /**
     * Subscribed delegates
     *
     * @return array
     */
    public function getSubscribedDelegates()
    {
        return array(
            array(
                'page' => '/frontend/',
                'delegate' => 'MembersPostLogin',
                'callback' => 'membersAuthSuccess'
            ),
            array(
                'page' => '/frontend/',
                'delegate' => 'MembersPostResetPassword',
                'callback' => 'membersAuthSuccess'
            ),
            array(
                'page' => '/frontend/',
                'delegate' => 'MembersLoginFailure',
                'callback' => 'membersAuthFailure'
            ),
            array(
                'page' => '/frontend/',
                'delegate' => 'MembersPasswordResetFailure',
                'callback' => 'membersAuthFailure'
            ),
            array(
                'page' => '/frontend/',
                'delegate' => 'FrontendParamsResolve',
                'callback' => 'frontendParamsResolve'
            ),
        );
    }

    /**
     * Members Authentication Success
     *
     * @uses MembersPostLogin
     * @uses MembersPostResetPassword
     * @param mixed $context
     */
    public function membersAuthSuccess($context)
    {
        if (ABF::instance()->isCurrentlyBanned()) {
            $this->getMembersDriver()->getMemberDriver()->logout();
            // Redirect to prevent resubmitting the form by reloading the page
            redirect(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : URL);
        } else {
            ABF::instance()->unregisterFailure();
        }
    }

    /**
     * Members Authentication Failure
     *
     * @uses MembersLoginFailure
     * @uses MembersPasswordResetFailure
     * @param mixed $context
     */
    public function membersAuthFailure($context)
    {
        ABF::instance()->registerFailure($context['username'], 'Members ABF');
        if (ABF::instance()->isCurrentlyBanned()) {
            // Redirect to prevent resubmitting the form by reloading the page
            redirect(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : URL);
        }
    }

    /**
     * frontendParamsResolve
     *
     * Add ABF status to the Param Pool
     *
     * @uses FrontendParamsResolve
     * @param string $context
     * @return void
     */
    public function frontendParamsResolve($context)
    {
        $context['params']['remote-address-banned'] = ABF::instance()->isCurrentlyBanned() ? 'yes' : 'no';
        $context['params']['remote-address-blacklisted'] = ABF::instance()->isBlackListed() ? 'yes' : 'no';
    }

    /**
     * Get Members driver
     */
    public function getMembersDriver()
    {
        if (!($this->MembersDriver instanceof extension_members)) {
            $this->MembersDriver = Symphony::ExtensionManager()->getInstance('members');
        }

        return $this->MembersDriver;
    }
}
