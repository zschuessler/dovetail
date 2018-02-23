<?php

namespace SquareBit\Dovetail\Api\Request;

use SquareBit\Dovetail\Api\Exception\InvalidRequest;

class Milestones extends RequestAbstract
{
    /**
     * Get All Milestones
     *
     * Returns all milestones.
     *
     * @param $options array An array of optional GET params.
     * @param $options['page'] int The page number to show, when paging.
     * @param $options['pageSize'] int The number of results to show per page.
     *
     * @return null|\SquareBit\Dovetail\Api\json
     */
    public function all($options = null)
    {
        return $this->apiClient->get('milestones.json', $options)->milestones;
    }

    /**
     * Get All Milestones for Project
     *
     * Gets all milestones for a project when given project ID.
     *
     * Request:
     * GET /projects/{project_id}/milestones.json?find=[all|completed|incomplete|late|upcoming]
     *
     * @param $projectId int The project ID.
     * @param $params
     * @throws InvalidRequest
     */
    public function allForProject($projectId, $options = null)
    {
        $this->assertValidResourceId($projectId, 'A valid project ID is required when getting milestones for a project.');

        return $this->apiClient->get(sprintf('projects/%s/milestones.json', $projectId), $options)->milestones;

    }

    /**
     * Get A Milestone
     *
     * Returns a milestone.
     *
     * @param $milestoneId int The milestone ID.
     *
     * @see https://developer.teamwork.com/milestones#get_a_single_mile
     *
     * @return null|\SquareBit\Dovetail\Api\json
     */
    public function get($milestoneId)
    {
        $this->assertValidResourceId($milestoneId, 'You must specify a valid milestone ID when getting a milestone.');

        return $this->apiClient->get(sprintf('milestones/%s.json', $milestoneId))->milestone;
    }

    /**
     * Create Milestone
     *
     * Creates a milestone when given a project ID and data array.
     *
     * @param $projectId int The project ID.
     * @param $options array
     *
     * @see https://developer.teamwork.com/milestones#create_a_single_m
     *
     * @return null|\SquareBit\Dovetail\Api\json
     */
    public function create($projectId, $options)
    {
        $this->assertValidResourceId($projectId, 'A valid project ID is required when creating a milestone.');

        /** @var $validator \Illuminate\Validation\Validator */
        $validator = \Validator::make($options, [
            'title'    => 'required',
            'deadline' => 'required',
            'responsible-party-ids' => 'required',
        ], [
            'title.required'    => '`title` is a required field when creating new milestone.',
            'deadline.required' => '`deadline` is a required field when creating new milestone.',
            'responsible-party-ids.required' => '`responsible-party-ids` is a required field when creating new milestone.',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequest($validator->getMessageBag()->first());
        }

        return $this->apiClient->post(sprintf('projects/%s/milestones.json', $projectId), [
            'milestone' => $options
        ]);
    }

    /**
     * Update Milestone
     *
     * Updates a milestone when given a valid milestone ID and attributes to update.
     *
     * @var $params array An array of parameters for the milestone
     *
     * @see https://developer.teamwork.com/milestones#update
     */
    public function update($milestoneId, $params)
    {
        $this->assertValidResourceId($milestoneId, 'You must specify a valid milestone ID when updating a milestone.');

        return $this->apiClient->put(sprintf('milestones/%s.json', $milestoneId), [
            'milestone' => $params
        ]);
    }


    /**
     * Delete Milestone
     *
     * Deletes a milestone when given a valid milestone ID.
     *
     * @param $milestoneId The milestone ID to delete.
     *
     * @see https://developer.teamwork.com/milestones#delete
     */
    public function delete($milestoneId)
    {
        $this->assertValidResourceId($milestoneId, 'You must specify a valid milestone ID when deleting a milestone.');

        return $this->apiClient->delete(sprintf('milestones/%s.json', $milestoneId));
    }

    /**
     * Mark Complete
     *
     * Marks milestone as complete when given milestone ID.
     *
     * @param $milestoneId int The milestone ID.
     *
     * @see https://developer.teamwork.com/milestones#complete
     *
     * @return object
     * @throws InvalidRequest
     */
    public function markComplete($milestoneId)
    {
        $this->assertValidResourceId($milestoneId, 'You must specify a valid milestone ID when marking as complete.');

        return $this->apiClient->put(sprintf('milestones/%s/complete.json', $milestoneId));
    }

    /**
     * Mark Uncomplete
     *
     * Marks milestone as uncomplete when given milestone ID.
     *
     * @param $milestoneId int The milestone ID.
     *
     * @see https://developer.teamwork.com/milestones#uncomplete
     *
     * @return object
     * @throws InvalidRequest
     */
    public function markUncomplete($milestoneId)
    {
        $this->assertValidResourceId($milestoneId, 'You must specify a valid milestone ID when marking as uncomplete.');

        return $this->apiClient->put(sprintf('milestones/%s/uncomplete.json', $milestoneId));
    }


}