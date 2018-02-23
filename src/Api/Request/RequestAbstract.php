<?php

namespace SquareBit\Dovetail\Api\Request;

use SquareBit\Dovetail\Api\Client as DovetailApiClient;
use SquareBit\Dovetail\Api\Exception\InvalidRequest;

/**
 * Class RequestAbstract
 *
 * Implements helper methods for all API Request classes.
 *
 * @package SquareBit\Dovetail\Api\Request
 */
class RequestAbstract
{
    /**
     * @var DovetailApiClient
     */
    public $apiClient;

    public function __construct(DovetailApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    /**
     * Assert Valid Resource ID
     *
     * Asserts the given ID is valid.
     *
     * @param $resourceId int|string The resource ID.
     * @param $messageOnFail string The message on failure.
     *
     * @throws InvalidRequest
     */
    public function assertValidResourceId($resourceId, $messageOnFail)
    {
        if (!is_string($resourceId) && !is_int($resourceId)) {
            throw new InvalidRequest($messageOnFail);
        }
    }

}