<?php

namespace SquareBit\Dovetail\Api\Request;

use SquareBit\Dovetail\Api\Exception\InvalidRequest;

class Projects extends RequestAbstract
{
    /**
     * Get All Projects
     *
     * Returns all projects.
     *
     * @param null $options
     *
     * @see https://developer.teamwork.com/projectsapi#retrieve_all_proj
     *
     * @return null|\SquareBit\Dovetail\Api\json
     */
    public function all($options = null)
    {
        return $this->apiClient->get('projects.json', $options)->projects;
    }

    /**
     * Get Project
     *
     * Gets a project when given project ID.
     *
     * @param $projectId int The project ID.
     * @param null|array $options Additional options to set.
     * @param bool $options['includePeople'] When true, also returns IDs of all people in response. Defaults to false.
     *
     * @see https://developer.teamwork.com/projectsapi#retrieve_a_single
     *
     * @return object
     * @throws InvalidRequest
     */
    public function get($projectId, $options = null)
    {
        $this->assertValidResourceId($projectId, 'You must specify a valid project ID when getting a project.');

        return $this->apiClient->get(sprintf('projects/%s.json', $projectId), $options)->project;
    }

    /**
     * Create New Project
     *
     * Creates a new project. On success, returns an object with the project ID.
     *
     * @see https://developer.teamwork.com/projectsapi#create_project
     */
    public function create($params)
    {
        /** @var $validator \Illuminate\Validation\Validator */
        $validator = \Validator::make($params, [
            'name' => 'required'
        ], [
            'name.required' => '`name` is a required field when creating new project.'
        ]);

        if ($validator->fails()) {
            throw new InvalidRequest($validator->getMessageBag()->first());
        }

        return $this->apiClient->post('projects.json', [
            'project' => $params
        ]);
    }

    /**
     * Update Project
     *
     * Updates a project when given project ID and data array.
     *
     * @param $projectId int The project ID.
     * @param $params array Project data to set.
     *
     * @see https://developer.teamwork.com/projectsapi#update_project
     *
     * @return null|object
     * @throws InvalidRequest
     * @throws \Exception
     * @throws \SquareBit\Dovetail\Api\Exception\NotAuthorizedException
     */
    public function update($projectId, $params)
    {
        $this->assertValidResourceId($projectId, 'You must specify a valid project ID when updating a project.');

        return $this->apiClient->put(sprintf('projects/%s.json', $projectId), [
            'project' => $params
        ]);
    }

    /**
     * Delete Project
     *
     * Deletes a project when given the project ID.
     *
     * @param $projectId int The project ID to delete.
     *
     * @see https://developer.teamwork.com/projectsapi#delete_project
     */
    public function delete($projectId)
    {
        $this->assertValidResourceId($projectId, 'You must specify a valid project ID when deleting a project.');

        return $this->apiClient->delete(sprintf('projects/%s.json', $projectId));
    }

    /**
     * Get Box
     *
     * Gets the Box folder and access information when given project ID.
     *
     * @param $projectId int The project ID.
     * @param $options array An array of optional GET params.
     * @param $options['page'] int The page number to show, when paging.
     * @param $options['pageSize'] int The number of results to show per page.
     * @return null|\SquareBit\Dovetail\Api\json
     */
    public function getBox($projectId)
    {
        return $this->apiClient->get(sprintf('projects/%s/box.json', $projectId));
    }

    /**
     * Get Project Rates
     *
     * Gets all individual user rates and default project rate.
     *
     * @param $projectId int The project ID.
     * @param $options array An array of optional GET params.
     * @param $options['page'] int The page number to show, when paging.
     * @param $options['pageSize'] int The number of results to show per page.
     * @return null|\SquareBit\Dovetail\Api\json
     * @throws InvalidRequest
     */
    public function getRates($projectId, $options = null)
    {
        $this->assertValidResourceId($projectId, 'You must specify a valid project ID when rates for a project.');

        return $this->apiClient->get(sprintf('projects/%s/rates.json', $projectId), $options);
    }

    /**
     * Set Project Rates
     *
     * Set individual user rates and the project default rate.
     *
     * Request:
     * POST to /projects/{$project_id}/rates.json
     *
     * Example:
     * $projects->setRates($projectId,
     *   "users" => [
     *     "12345": {
     *       "rate": 60
     *     ],
     *     "12346": [
     *       "rate": 35
     *     ]
     *   ],
     *   "project-default" => 20
     * );
     *
     * @param $projectId int The project ID.
     * @param $rates array An array of users to set rates for, as well as project default.
     * @return null|\SquareBit\Dovetail\Api\json
     * @throws InvalidRequest
     */
    public function setRates($projectId, $ratesArray)
    {
        $this->assertValidResourceId($projectId, 'You must specify a valid project ID setting rates for a project.');

        return $this->apiClient->post(
            sprintf('projects/%s/rates.json', $projectId), [
                'rates' => $ratesArray
            ]);
    }

    /**
     * Get All Starred Projects
     *
     * Returns all projects that have been starred.
     *
     * @return null|\SquareBit\Dovetail\Api\json
     */
    public function allStarred()
    {
        return $this->apiClient->get('/projects/starred.json')->projects;
    }

    /**
     * Apply Star
     *
     * Sets the given project as "starred"
     *
     * @param $projectId int The project ID.
     * @return null|\SquareBit\Dovetail\Api\json
     * @throws InvalidRequest
     */
    public function applyStar($projectId)
    {
        $this->assertValidResourceId($projectId, 'You must specify a valid project ID when starring a project.');

        return $this->apiClient->put(sprintf('/projects/%s/star.json', $projectId));
    }

    /**
     * Remove Star
     *
     * Removes a project star when given project ID.
     *
     * @param $projectId int The project ID.
     * @return null|\SquareBit\Dovetail\Api\json
     * @throws InvalidRequest
     */
    public function removeStar($projectId)
    {
        $this->assertValidResourceId($projectId, 'You must specify a valid project ID when removing a project star.');

        return $this->apiClient->put(sprintf('/projects/%s/unstar.json', $projectId));
    }
}