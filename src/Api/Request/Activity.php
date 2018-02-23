<?php

namespace SquareBit\Dovetail\Api\Request;

use SquareBit\Dovetail\Api\Exception\InvalidRequest;

/**
 * Activity Request Endpoint
 *
 * @package SquareBit\Dovetail\Api\Request
 * @see https://developer.teamwork.com/activity
 */
class Activity extends RequestAbstract
{
    /**
     * Get Latest Activity
     *
     * Lists the latest activity across all projects ordered chronologically
     *
     * @param $options array An array of options for the query.
     * @param $options['maxItems'] integer The number of items to return Default is 60, max is 200.
     * @param $options['onlyStarred'] boolean Returns only starred items when true. Default is false.
     *
     * @see https://developer.teamwork.com/activity#latest_activity_a
     */
    public function all($query = null)
    {
        return $this->apiClient->get('latestActivity.json', $query)->activity;
    }

    /**
     * Get Latest Project Activity
     *
     * Returns all activity for a given project ID.
     *
     * @param $projectId The Teamwork project ID.
     * @param $options An array of options for the query.
     * @param $options['maxItems'] The number of items to return Default is 60, max is 200.
     *
     * @see https://developer.teamwork.com/activity#list_latest_activ
     */
    public function forProject($projectId, $options = null)
    {
        $this->assertValidResourceId($projectId, 'You must specify a valid project ID when getting activity for a project.');

        return $this->apiClient->get(sprintf('projects/%s/latestActivity.json', $projectId), $options)->activity;
    }

    /**
     * Delete Activity Entry
     *
     * Deletes an activity log entry when given the activity ID.
     * Returns HTTP 200 status code on success.
     *
     * @param $activityId The Teamwork activity ID.
     *
     * @see https://developer.teamwork.com/activity#delete_an_activit
     */
    public function delete($activityId)
    {
        $this->assertValidResourceId($activityId, 'You must specify a valid activity ID when deleting an activity.');

        return $this->apiClient->delete(sprintf('/activity/%s.json', $activityId));
    }
}