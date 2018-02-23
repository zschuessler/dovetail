<?php

namespace SquareBit\Dovetail\Api\Request;

use SquareBit\Dovetail\Api\Exception\InvalidRequest;

/**
 * Task Lists API Endpoint
 *
 * @package SquareBit\Dovetail\Api\Request
 * @see https://developer.teamwork.com/tasklists
 */
class TaskLists extends RequestAbstract
{
    /**
     * Get All Task Lists
     *
     * Gets all paginated task lists.
     *
     * @param null|array $options
     *
     * @see https://developer.teamwork.com/tasklists
     *
     * @return array An array of task list objects.
     */
    public function all($options = null)
    {
        return $this->apiClient->get('tasklists.json', $options)->{'tasklists'};
    }

    /**
     * Get All Task Lists for Project
     *
     * Gets all paginated task lists for a project when given project ID.
     *
     * @param null|array $options
     *
     * @see https://developer.teamwork.com/tasklists#get_all_task_list
     *
     * @return array An array of task list objects.
     */
    public function allForProject($projectId, $options = null)
    {
        $this->assertValidResourceId($projectId, 'A valid project ID is required when getting task lists for project.');

        return $this->apiClient->get(sprintf('projects/%s/tasklists.json', $projectId), $options)->{'tasklists'};
    }

    /**
     * Get Task List
     *
     * Gets a task list when given task list ID.
     *
     * @param $taskListId int The task list ID.
     * @param null|array $options
     *
     * @see https://developer.teamwork.com/tasklists#retrieve_single_t
     *
     * @return object A task list object.
     * @throws InvalidRequest
     */
    public function get($taskListId, $options = null)
    {
        $this->assertValidResourceId($taskListId, 'A valid task list ID is required when getting a task list.');

        return $this->apiClient->get(sprintf('tasklists/%s.json', $taskListId), $options)->{'todo-list'};
    }

    /**
     * Create Task List
     *
     * Creates a task list when given the project ID and data array.
     *
     * @param $projectId int The project ID.
     * @param array $options
     *
     * @see https://developer.teamwork.com/tasklists#create_list
     *
     * @return object An object with the task list ID.
     * @throws InvalidRequest
     */
    public function create($projectId, $options)
    {
        $this->assertValidResourceId($projectId, 'A valid project ID is required when creating a task list.');

        /** @var $validator \Illuminate\Validation\Validator */
        $validator = \Validator::make($options, [
            'name' => 'required'
        ], [
            'name.required'    => '`name` is a required field when creating new task list.',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequest($validator->getMessageBag()->first());
        }

        $response = $this->apiClient->post(sprintf('projects/%s/tasklists.json', $projectId), [
            'todo-list' => $options
        ]);

        // Add `id` field for consistency, as Teamwork does not include it
        if (!isset($response->id)) {
            $response->id = $response->TASKLISTID;
        }

        return $response;
    }

    /**
     * Update Task List
     *
     * Updates a task list when given the task list ID and data array.
     *
     * @param $taskListId int The task list ID.
     * @param array $options
     *
     * @see https://developer.teamwork.com/todolistitems#update_list
     *
     * @return object
     * @throws InvalidRequest
     */
    public function update($taskListId, $options)
    {
        $this->assertValidResourceId($taskListId, 'A valid task list ID is required when updating a task list.');

        return $this->apiClient->put(sprintf('tasklists/%s.json', $taskListId), [
            'todo-list' => $options
        ]);
    }

    /**
     * Delete Task List
     *
     * Deletes a task list when given a valid task list ID.
     *
     * @param $taskListId The task list ID to delete.
     *
     * @see https://developer.teamwork.com/tasklists#delete_a_task_lis
     *
     * @return object
     * @throws InvalidRequest
     */
    public function delete($taskListId)
    {
        $this->assertValidResourceId($taskListId, 'You must specify a valid task list ID when deleting a task.');

        return $this->apiClient->delete(sprintf('tasklists/%s.json', $taskListId));
    }
}