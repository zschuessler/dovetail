<?php

namespace SquareBit\Dovetail;

use GuzzleHttp\Client as Guzzle;
use SquareBit\Dovetail\Api\Client as DovetailApiClient;

/**
 * Class Dovetail
 *
 * An API wrapper for the Teamwork.com API, for Laravel 5.
 *
 * Example with default configuration:
 *
 * ```
 * $dovetail   = new \SquareBit\Dovetail\Dovetail;
 * $milestones = $dovetail->milestones()->all();
 * ```
 *
 * @method \SquareBit\Dovetail\Api\Request\Account account The account endpoint.
 * @method \SquareBit\Dovetail\Api\Request\CurrentUser currentUser The me endpoint.
 * @method \SquareBit\Dovetail\Api\Request\Projects projects The projects endpoint.
 * @method \SquareBit\Dovetail\Api\Request\Activity activity The activity endpoint.
 * @method \SquareBit\Dovetail\Api\Request\Billing billing The billing endpoint.
 * @method \SquareBit\Dovetail\Api\Request\Comments comments The comments endpoint.
 * @method \SquareBit\Dovetail\Api\Request\Companies companies The companies endpoint.
 * @method \SquareBit\Dovetail\Api\Request\Links links The links endpoint.
 * @method \SquareBit\Dovetail\Api\Request\Messages messages The messages endpoint.
 * @method \SquareBit\Dovetail\Api\Request\MessageReplies messageReplies The message replies endpoint.
 * @method \SquareBit\Dovetail\Api\Request\Milestones milestones The milestones endpoint.
 * @method \SquareBit\Dovetail\Api\Request\Notebooks notebooks The notebooks endpoint.
 * @method \SquareBit\Dovetail\Api\Request\People people The people endpoint.
 * @method \SquareBit\Dovetail\Api\Request\Risks risks The risks endpoint.
 * @method \SquareBit\Dovetail\Api\Request\Tags tags The tags endpoint.
 * @method \SquareBit\Dovetail\Api\Request\Tasks tasks The tasks endpoint.
 * @method \SquareBit\Dovetail\Api\Request\Workload workload The workload endpoint.
 *
 * @author Zachary Schuessler <zlschuessler@gmail.com>
 * @package SquareBit\Dovetail
 *
 * @see https://squarebit.io/zschuessler/dovetail-teamwork-api-for-laravel/documentation/getting-started/api-request-cheat-sheet
 */
class Dovetail
{
    /**
     * @var DovetailApiClient
     */
    public $apiClient;

    /**
     * Dovetail constructor.
     *
     * Accepts an instance of DovetailApiClient for setting credentials and HTTP handler,
     * or defaults to the application configuration if none set.
     *
     * Defaults to /config/dovetail.php:
     * `$dovetail = new \SquareBit\Dovetail\Dovetail;`
     *
     * Create a new instance with specific credentials:
     * ```
     * $dovetail = new \SquareBit\Dovetail\Dovetail(
     *     new \SquareBit\Dovetail\Api\Client('my-api-key', 'https://myDomain.teamwork.com')
     * );
     * ```
     *
     * @param DovetailApiClient|NULL $httpClient
     */
    public function __construct(DovetailApiClient $httpClient = NULL)
    {
        $this->apiClient = $httpClient ?? new DovetailApiClient(null, null, new Guzzle);
    }

    /**
     * Call Magic Method
     *
     * Allows us to create instances of the API requests in this format:
     * `$dovetail->endpointName()->methodName();`
     *
     * @param $name
     * @param $arguments
     *
     * @return object
     * @throws \ReflectionException
     */
    public function __call($name, $arguments)
    {
        // Get class name of API request
        $className = ucwords($name);
        $namespace = __NAMESPACE__;
        $fqName    = sprintf('%s\Api\Request\%s', $namespace, $className);

        // Instantiate it
        $apiRequestClass    = new \ReflectionClass($fqName);
        $apiRequestInstance = $apiRequestClass->newInstance($this->apiClient);

        return $apiRequestInstance;
    }

    /**
     * Get API Client
     *
     * Useful for getting the current API client, which could be used to edit credentials,
     * domain, or HTTP handler.
     * \
     * @return NULL|DovetailApiClient
     */
    public function getApiClient()
    {
        return $this->apiClient;
    }

    /**
     * Get Last HTTP Response
     *
     * Gets the last HTTP response the handler received.
     * Useful for getting HTTP data such as headers.
     *
     * @return \GuzzleHttp\Response
     */
    public function getLastResponse()
    {
        return $this->getApiClient()->getLastResponse();
    }

    /**
     * Get Last Request
     *
     * Gets the last HTTP request the handler initiated.
     * Useful for debugging requests.
     *
     * @return Guzzle
     */
    public function getLastRequest()
    {
        return $this->getApiClient()->getLastRequest();
    }
}