<?php

namespace SquareBit\Dovetail\Api\Request;

use SquareBit\Dovetail\Api\Exception\InvalidRequest;

/**
 * Tasks API Endpoint
 *
 * @package SquareBit\Dovetail\Api\Request
 * @see https://developer.teamwork.com/todolistitems
 */
class Tasks extends RequestAbstract
{
    /**
     * Get All Tasks
     *
     * Gets all paginated tasks.
     *
     * @param null|array $options
     *
     * @see https://developer.teamwork.com/todolistitems#retrieve_all_task
     *
     * @return array An array of task objects.
     */
    public function all($options = null)
    {
        return $this->apiClient->get('tasks.json', $options)->{'todo-items'};
    }

    /**
     * Get All Tasks for a Project
     *
     * Gets all tasks when given the project ID.
     *
     * @param $projectId int The project ID.
     * @param null|array $options
     *
     * @see https://developer.teamwork.com/todolistitems#retrieve_all_task
     *
     * @return array An array of task objects.
     * @throws InvalidRequest
     */
    public function allForProject($projectId, $options = null)
    {
        $this->assertValidResourceId($projectId, 'A valid project ID is required when getting tasks for project.');

        return $this->apiClient->get(sprintf('projects/%s/tasks.json', $projectId), $options)->{'todo-items'};
    }

    /**
     * Get All Tasks for a Task List
     *
     * Gets all tasks when given the task list ID.
     *
     * @param $taskListId int The task list ID.
     * @param null|array $options
     *
     * @see https://developer.teamwork.com/todolistitems#retrieve_all_task
     *
     * @return array An array of task objects.
     * @throws InvalidRequest
     */
    public function allForTaskList($taskListId, $options = null)
    {
        $this->assertValidResourceId($taskListId, 'A valid task list ID is required when getting tasks for task list.');

        return $this->apiClient->get(sprintf('tasklists/%s/tasks.json', $taskListId), $options)->{'todo-items'};
    }

    /**
     * Get Task
     *
     * Gets a task when given task ID.
     *
     * @param $taskId int The task ID.
     * @param null|array $options
     *
     * @see https://developer.teamwork.com/todolistitems#retrieve_a_task
     *
     * @return object A task object.
     * @throws InvalidRequest
     */
    public function get($taskId, $options = null)
    {
        $this->assertValidResourceId($taskId, 'A valid task ID is required when getting a task.');

        return $this->apiClient->get(sprintf('tasks/%s.json', $taskId), $options)->{'todo-item'};
    }

    /**
     * Get Task Dependencies
     *
     * Gets a task dependency list when given the task ID.
     *
     * @param $taskId int The task ID.
     * @param null|array $options
     *
     * @see https://developer.teamwork.com/todolistitems#retrieve_task_dep
     *
     * @return array An array of task objects.
     * @throws InvalidRequest
     */
    public function getTaskDependencies($taskId, $options = null)
    {
        $this->assertValidResourceId($taskId, 'A valid task ID is required when getting task dependencies.');

        return $this->apiClient->get(sprintf('tasks/%s/dependencies.json', $taskId), $options)->dependents;
    }

    /**
     * Create Task
     *
     * Creates a task when given the task list ID and data array.
     *
     * @param $taskListId int The task list ID.
     * @param array $options
     *
     * @see https://developer.teamwork.com/todolistitems#add_a_task
     *
     * @return object An object with the task ID and affected task IDs.
     * @throws InvalidRequest
     */
    public function create($taskListId, $options)
    {
        $this->assertValidResourceId($taskListId, 'A valid task list ID is required when creating a task.');

        /** @var $validator \Illuminate\Validation\Validator */
        $validator = \Validator::make($options, [
            'content' => 'required'
        ], [
            'content.required'    => '`content` is a required field when creating new task.',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequest($validator->getMessageBag()->first());
        }

        return $this->apiClient->post(sprintf('tasklists/%s/tasks.json', $taskListId), [
            'todo-item' => $options
        ]);
    }

    /**
     * Update Task
     *
     * Updates a task when given the task ID and data array.
     *
     * @param $taskId int The task ID.
     * @param array $options
     *
     * @see https://developer.teamwork.com/todolistitems#edit_a_task
     *
     * @return object An object with the task ID and affected task IDs.
     * @throws InvalidRequest
     */
    public function update($taskId, $options)
    {
        $this->assertValidResourceId($taskId, 'A valid task ID is required when updating a task.');

        /** @var $validator \Illuminate\Validation\Validator */
        $validator = \Validator::make($options, [
            'content' => 'required'
        ], [
            'content.required'    => '`content` is a required field when updating a task.',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequest($validator->getMessageBag()->first());
        }

        return $this->apiClient->put(sprintf('tasks/%s.json', $taskId), [
            'todo-item' => $options
        ]);
    }

    /**
     * Delete Task
     *
     * Deletes a task when given a valid task ID.
     *
     * @param $taskId The task ID to delete.
     *
     * @see https://developer.teamwork.com/todolistitems#destroy_a_task
     *
     * @return object An object with affected task IDs.
     * @throws InvalidRequest
     */
    public function delete($taskId)
    {
        $this->assertValidResourceId($taskId, 'You must specify a valid task ID when deleting a task.');

        return $this->apiClient->delete(sprintf('tasks/%s.json', $taskId));
    }

    /**
     * Mark Complete
     *
     * Marks a task complete when given a valid task ID.
     *
     * @param $taskId The task ID to mark complete.
     *
     * @see https://developer.teamwork.com/todolistitems#mark_a_task_compl
     *
     * @return object An object with affected task IDs.
     * @throws InvalidRequest
     */
    public function markComplete($taskId)
    {
        $this->assertValidResourceId($taskId, 'You must specify a valid task ID when marking task complete.');

        return $this->apiClient->put(sprintf('tasks/%s/complete.json', $taskId));
    }

    /**
     * Mark Uncomplete
     *
     * Marks a task uncomplete when given a valid task ID.
     *
     * @param $taskId The task ID to mark uncomplete.
     *
     * @see https://developer.teamwork.com/todolistitems#mark_a_task_uncom
     *
     * @return object An object with affected task IDs.
     * @throws InvalidRequest
     */
    public function markUncomplete($taskId)
    {
        $this->assertValidResourceId($taskId, 'You must specify a valid task ID when marking task uncomplete.');

        return $this->apiClient->put(sprintf('tasks/%s/uncomplete.json', $taskId));
    }

    /**
     * Get Followers
     *
     * Gets task followers when given a valid task ID.
     *
     * @param $taskId The task ID.
     *
     * @see https://developer.teamwork.com/todolistitems#get_task_follower
     *
     * @return object An object with affected task IDs.
     * @throws InvalidRequest
     */
    public function getFollowers($taskId)
    {
        $this->assertValidResourceId($taskId, 'You must specify a valid task ID when getting task followers.');

        return $this->apiClient->get(sprintf('tasks/%s/followers.json', $taskId));
    }

    /**
     * Set Followers
     *
     * Sets task followers when given a valid task ID and data array.
     *
     * @param $taskId The task ID.
     *
     * @see https://developer.teamwork.com/todolistitems#set_task_follower
     *
     * @return object An object with affected task IDs.
     * @throws InvalidRequest
     */
    public function setFollowers($taskId, $options)
    {
        $this->assertValidResourceId($taskId, 'You must specify a valid task ID when setting task followers.');

        return $this->apiClient->put(sprintf('tasks/%s.json', $taskId), [
            'todo-item' => $options
        ]);
    }
}