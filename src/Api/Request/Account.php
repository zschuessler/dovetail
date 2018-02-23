<?php

namespace SquareBit\Dovetail\Api\Request;

use SquareBit\Dovetail\Api\Exception\InvalidRequest;

/**
 * Account API Endpoint
 *
 * @package SquareBit\Dovetail\Api\Request
 * @see https://developer.teamwork.com/account
 */
class Account extends RequestAbstract
{
    /**
     * Get Account Details
     *
     * Gets broad information on the Teamwork instance being accessed.
     *
     * @see https://developer.teamwork.com/account#get_account_detai
     */
    public function getDetails()
    {
        return $this->apiClient->get('account.json')->account;
    }

    /**
     * Get Account Authentication
     *
     * Returns account information from the `getDetails` endpoint, but also additional information
     * about the current user that has authenticated.
     *
     * @see https://developer.teamwork.com/account#the_'authenti
     */
    public function getAuthentication()
    {
        return $this->apiClient->get('authenticate.json')->account;
    }
}