<?php

namespace SquareBit\Dovetail\Api\Request;

use SquareBit\Dovetail\Api\Exception\InvalidRequest;

class Comments extends RequestAbstract
{
    /**
     * @var array An array of valid resource types which may have comments.
     */
    public $resourceTypes = ['links', 'milestones', 'files', 'notebooks', 'tasks'];

    /**
     * Assert Resource Type is Valid
     *
     * Asserts the given resource type is within the $resourceTypes array for this class.
     *
     * @param $resourceType
     *
     * @throws InvalidRequest
     */
    public function assertResourceTypeIsValid($resourceType)
    {
        if (!in_array($resourceType, $this->resourceTypes)) {
            throw new InvalidRequest('The resource type specified is invalid: ' . $resourceType);
        }
    }

    /**
     * Get Recent
     *
     * Gets recent comments when a resource type and ID are both specified.
     *
     * Valid resoure types: links, milestones, files, notebooks, tasks
     *
     * @param $resourceType string links, milestones, files, notebooks or tasks
     * @param $resourceId int The resource ID to retrieve.
     * @param null $query
     * @return null|\SquareBit\Dovetail\Api\json
     *
     * @see https://developer.teamwork.com/comments#retreiving_recent
     */
    public function recent($resourceType, $resourceId, $query = null)
    {
        $this->assertResourceTypeIsValid($resourceType);
        $this->assertValidResourceId($resourceId, 'You must specify a resource ID when getting recent comments');

        return $this->apiClient->get(sprintf('%s/%s/comments.json', $resourceType, $resourceId), $query)->comments;
    }

    /**
     * POST /{resource}/{resource_id}/comments.json
     */

    /**
     * Create Comment
     *
     * Creates a comment when given the resource type, ID, and an options array.
     *
     * @param $resourceType string A valid resource type.
     * @param $resourceId int The resource ID.
     * @param $options array An array of options.
     *
     * @return null|\SquareBit\Dovetail\Api\json
     * @throws InvalidRequest
     */
    public function create($resourceType, $resourceId, $options)
    {
        $this->assertResourceTypeIsValid($resourceType);
        $this->assertValidResourceId($resourceId, 'You must specify a resource ID when creating a comment');

        /** @var $validator \Illuminate\Validation\Validator */
        $validator = \Validator::make($options, [
            'body' => 'required',
        ], [
            'body.required' => '`body` is a required field when creating new comment.'
        ]);

        if ($validator->fails()) {
            throw new InvalidRequest($validator->getMessageBag()->first());
        }

        return $this->apiClient->post(sprintf('%s/%s/comments.json', $resourceType, $resourceId), [
            'comment' => $options
        ]);
    }


    /**
     * Get Comment
     *
     * Gets a comment when given the comment ID.
     *
     * @see https://developer.teamwork.com/comments#retrieving_a_spec
     */
    public function get($commentId, $query = null)
    {
        $this->assertValidResourceId($commentId, 'You must specify a valid comment ID when getting a comment.');

        return $this->apiClient->get(sprintf('comments/%s.json', $commentId), $query)->comment;
    }

    /**
     * Update Comment
     *
     * Updates a comment when given a valid comment ID and comment attributes to update.
     *
     * @var $params array An array of parameters for the comment
     *
     * @see https://developer.teamwork.com/comments#updating_a_commen
     */
    public function update($commentId, $options)
    {
        $this->assertValidResourceId($commentId, 'You must specify a valid comment ID when updating a comment.');

        /** @var $validator \Illuminate\Validation\Validator */
        $validator = \Validator::make($options, [
            'body' => 'required',
        ], [
            'body.required' => '`body` is a required field when updating a comment.'
        ]);

        if ($validator->fails()) {
            throw new InvalidRequest($validator->getMessageBag()->first());
        }

        return $this->apiClient->put(sprintf('comments/%s.json', $commentId), [
            'comment' => $options
        ]);
    }

    /**
     * Mark Comment as Read
     *
     * Marks a comment as read for the currently authenticated user.
     *
     * @param $commentId int The comment ID.
     *
     * @see https://developer.teamwork.com/comments#mark_a_comment_as
     */
    public function markRead($commentId)
    {
        $this->assertValidResourceId($commentId, 'You must specify a valid comment ID when marking a comment as read.');

        return $this->apiClient->put(sprintf('comments/%s/markread.json', $commentId));
    }

    /**
     * Delete Comment
     *
     * Deletes a comment when given a valid comment ID.
     *
     * @param $commentId The comment ID to delete.
     */
    public function delete($commentId)
    {
        $this->assertValidResourceId($commentId, 'You must specify a valid comment ID deleting a comment.');

        return $this->apiClient->delete(sprintf('comments/%s.json', $commentId));
    }
}