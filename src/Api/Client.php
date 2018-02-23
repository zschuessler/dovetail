<?php

namespace SquareBit\Dovetail\Api;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Request as GuzzleRequest;
use GuzzleHttp\Response as GuzzleResponse;
use SquareBit\Dovetail\Api\Exception\NotAuthorizedException;

/**
 * Class Client
 *
 * An API client that contains the credentials and HTTP handler for all API requests.
 *
 * You may pass an instance of this into the Dovetail class to set custom credentials and handlers
 * on each request:
 *
 * ```
 * $dovetail = new \SquareBit\Dovetail\Dovetail(
 *     new \SquareBit\Dovetail\Api\Client('my-api-key', 'https://myDomain.teamwork.com')
 * );
 *
 * $dovetail2 = new \SquareBit\Dovetail\Dovetail(
 *     new \SquareBit\Dovetail\Api\Client('another-key', 'https://anotherDomain.teamwork.com')
 * );
 * ```
 *
 * @package SquareBit\Dovetail\Api
 */
class Client
{

    /**
     * @var GuzzleClient
     */
    public $httpClient;

    /**
     * @var \GuzzleHttp\Response
     */
    public $httpResponse;

    /**
     * API Key - The Teamwork.com API Key.
     *
     * @var null |string
     */
    protected $apiKey;

    /**'API Url - The Teamwork.com API Url.
     *
     * @var null|string
     */
    protected $apiUrl;

    /**
     * Client constructor.
     *
     * @param $apiKey string|null The API key for the Teamwork.com user.
     * @param $apiUrl string|null The API url for the Teamwork.com account.
     * @param $client GuzzleClient|NULL
     */
    public function __construct($apiKey = null, $apiUrl = null, GuzzleClient $client = NULL)
    {
        $this->httpClient = $client ?? new GuzzleClient;

        $this->apiKey = $apiKey ?? config('dovetail.teamwork_api.key');

        $this->apiUrl = $apiUrl ?? 'https://' . config('dovetail.teamwork_api.domain');
    }

    /**
     * Set API Key
     *
     * Sets the Teamwork.com API key.
     *
     * @param $apiKey string
     *
     * @return $this
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    /**
     * Set API Url
     *
     * Sets the Teamwork.com API url.
     *
     * @param $apiUrl string
     *
     * @return $this
     */
    public function setApiUrl($apiUrl)
    {
        $this->apiUrl = $apiUrl;

        return $this;
    }

    /**
     * Get Last Request
     *
     * Gets the last Guzzle request.
     *
     * @return GuzzleClient
     */
    public function getLastRequest()
    {
        return $this->httpClient;
    }

    /**
     * Get Last Response
     *
     * Gets the last Guzzle response.
     *
     * @return GuzzleResponse
     */
    public function getLastResponse()
    {
        return $this->httpResponse;
    }

    /**
     * POST Request
     *
     * Initiates a POST request for the given endpoint.
     *
     * @param $endpoint string
     * @param $options null|array
     *
     * @return null|object
     * @throws NotAuthorizedException
     * @throws \Exception
     */
    public function post($endpoint, $options = null)
    {
        return $this->sendTeamworkRequest('post', $endpoint, $options);
    }

    /**
     * PUT Request
     *
     * Initiates a PUT request for the given endpoint.
     *
     * @param $endpoint string
     * @param $options null|array
     *
     * @return null|object
     * @throws NotAuthorizedException
     * @throws \Exception
     */
    public function put($endpoint, $options = null)
    {
        return $this->sendTeamworkRequest('put', $endpoint, $options);
    }

    /**
     * DELETE Request
     *
     * Initiates a DELETE request for the given endpoint.
     *
     * @param $endpoint string
     *
     * @return null|object
     * @throws NotAuthorizedException
     * @throws \Exception
     */
    public function delete($endpoint)
    {
        return $this->sendTeamworkRequest('delete', $endpoint);
    }

    /**
     * GET Request
     *
     * Initiates a GET request for the given endpoint.
     *
     * @param $endpoint string
     * @param $options null|array
     *
     * @return null|object
     * @throws NotAuthorizedException
     * @throws \Exception
     */
    public function get($endpoint, $options = null)
    {
        $requestUri = $endpoint;

        // Create query string
        if ($options) {
            $queryString = '?';

            foreach ($options as $queryKey => $queryValue) {
                if (is_bool($queryValue)) {
                    $queryString .= sprintf('%s=%s&', $queryKey, var_export($queryValue, true));
                } else {
                    $queryString .= sprintf('%s=%s&', $queryKey, $queryValue);
                }
            }
            $requestUri .= str_replace_last('&', '', $queryString);
        }

        return $this->sendTeamworkRequest('get', $requestUri);
    }

    /**
     * Send Teamwork Request
     *
     * A wrapper for sending a request. It sets the remote URI to request, as well as the
     * `auth` header required for authentication.
     *
     * @param $requestType string The request type. Can be `get`, `delete`, `post`, or `put`
     * @param $fullEndpoint string  The full endpoint. e.g.: `account.json`
     * @param $options null|array The json body to submit. Used for update and create requests.
     *
     * @return json|null
     */
    public function sendTeamworkRequest($requestType, $fullEndpoint, $options = null)
    {
        $remoteUri     = $this->apiUrl . '/' . $fullEndpoint;
        $requestParams = ['auth' => [$this->apiKey, 'X']];

        if ($options) {
            $requestParams = $requestParams + ['body' => json_encode($options)];
        }

        try {
            $this->httpResponse = $this->httpClient->{$requestType}($remoteUri, $requestParams);
        } catch (\Exception $e) {
            if (401 === $e->getCode()) {
                throw new NotAuthorizedException(
                    sprintf('Teamwork API key `%s` does not have permission for this request.', $this->apiKey )
                );
            }
            throw new \Exception($e->getMessage(), $e->getCode(), $e->getPrevious());
        }

        $jsonResponse = json_decode($this->httpResponse->getBody()->getContents());

        return $jsonResponse;
    }
}