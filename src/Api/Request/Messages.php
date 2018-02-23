<?php

namespace SquareBit\Dovetail\Api\Request;

use SquareBit\Dovetail\Api\Exception\InvalidRequest;

class Messages extends RequestAbstract
{

    /**
     * Get All Messages
     *
     * **Warning: This endpoint is not documented on Teamwork and may be volatile.**
     *
     * Gets all messages across all projects.
     *
     * @return null|\SquareBit\Dovetail\Api\json
     * @throws InvalidRequest
     */
    public function all()
    {
        return $this->apiClient->get('posts.json')->posts;
    }

    /**
     * Get A Message
     *
     * Returns a message when given a valid ID.
     *
     * @param $messageId int The message ID.
     *
     * @see https://developer.teamwork.com/messages#retrieve_a_single
     *
     * @return null|\SquareBit\Dovetail\Api\json
     */
    public function get($messageId)
    {
        $this->assertValidResourceId($messageId, 'A valid message ID is required when trying to get a message by ID.');

        return $this->apiClient->get(sprintf('posts/%s.json', $messageId))->post;
    }

    /**
     * Create Message
     *
     * Creates a message when given the project ID and options array.
     *
     * @param $projectId int The project ID.
     * @param $optionsArray array An array of optoins.
     *
     * @see https://developer.teamwork.com/messages#create_a_message
     *
     * @return mixed
     * @throws InvalidRequest
     */
    public function create($projectId, $options)
    {
        $this->assertValidResourceId($projectId, 'A valid project ID is required when trying to create a message.');

        /** @var $validator \Illuminate\Validation\Validator */
        $validator = \Validator::make($options, [
            'title'    => 'required',
            'body'    => 'required',
        ], [
            'title.required'   => '`title` is a required field when creating new message.',
            'body.required'   => '`body` is a required field when creating new message.',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequest($validator->getMessageBag()->first());
        }

        return $this->apiClient->post(sprintf('projects/%s/posts.json', $projectId), [
            'post' => $options
        ]);
    }

    /**
     * Get All Messages for Project Category
     *
     * Gets all messages when given the project and category IDs.
     *
     * @param $projectId int The project ID.
     * @param $categoryId int The category ID.
     * @throws InvalidRequest
     *
     * @see https://developer.teamwork.com/messages#retrieve_messages
     *
     * @return null|\SquareBit\Dovetail\Api\json
     */
    public function allForProjectCategory($projectId, $categoryId)
    {
        $this->assertValidResourceId($projectId, 'A valid project ID is required when getting messages for a project category.');
        $this->assertValidResourceId($categoryId, 'A valid category ID is required when getting messages for a project category.');

        return $this->apiClient->get(sprintf('projects/%s/cat/%s/posts.json', $projectId, $categoryId))->posts;
    }

    /**
     * Get Latest Messages for Project
     *
     * Gets latest 25 messages when given the project ID.
     *
     * @param $projectId int The project ID.
     * @throws InvalidRequest
     *
     * @see https://developer.teamwork.com/messages#retrieve_latest_m
     *
     * @return null|\SquareBit\Dovetail\Api\json
     */
    public function latestForProject($projectId)
    {
        $this->assertValidResourceId($projectId, 'A valid project ID is required when getting messages for a project.');

        return $this->apiClient->get(sprintf('projects/%s/posts.json', $projectId))->posts;
    }

    /**
     * Update Message
     *
     * Updates a message when given a valid message ID and message attributes to update.
     *
     * @var $params array An array of parameters for the message
     *
     * @see https://developer.teamwork.com/comments#update_message
     */
    public function update($messageId, $params)
    {
        $this->assertValidResourceId($messageId, 'You must specify a valid message ID when updating a message.');

        return $this->apiClient->put(sprintf('messages/%s.json', $messageId), [
            'post' => $params
        ]);
    }

    /**
     * Delete Message
     *
     * Deletes a message when given a valid message ID.
     *
     * @see https://developer.teamwork.com/messages#destroy_message
     *
     * @param $messageId The message ID to delete.
     */
    public function delete($messageId)
    {
        $this->assertValidResourceId($messageId, 'You must specify a valid message ID when deleting a message.');

        return $this->apiClient->delete(sprintf('messages/%s.json', $messageId));
    }

    /**
     * Mark Message as Read
     *
     * Marks a message as read for the currently authenticated user.
     *
     * @param $messageId The message ID
     *
     * @see https://developer.teamwork.com/messages#mark_message_read
     */
    public function markRead($messageId)
    {
        $this->assertValidResourceId($messageId, 'You must specify a valid message ID when marking a message as read.');

        return $this->apiClient->put(sprintf('messages/%s/markread.json', $messageId));
    }

    /**
     * Archive a Message
     *
     * Marks a message as archived when given a valid message ID.
     *
     * @param $messageId The message ID
     *
     * @see https://developer.teamwork.com/messages#archive_a_message
     */
    public function archive($messageId)
    {
        $this->assertValidResourceId($messageId, 'You must specify a valid message ID when marking a message as archived.');

        return $this->apiClient->put(sprintf('messages/%s/archive.json', $messageId));
    }

    /**
     * Unarchive a Message
     *
     * Marks a message as not archived when given a valid message ID.
     *
     * @param $messageId The message ID
     *
     * @see https://developer.teamwork.com/messages#un-archive_a_mess
     */
    public function unarchive($messageId)
    {
        $this->assertValidResourceId($messageId, 'You must specify a valid message ID when marking a message as unarchived.');

        return $this->apiClient->put(sprintf('messages/%s/unarchive.json', $messageId));
    }

    /**
     * Create New Message Reply
     *
     * Creates a message reply when given the message ID.
     * HTML may be passed without specifying content type.
     *
     * @param $messageId id A message Id to specify as the parent to reply to.
     * @param $params array An array of parameters for the new message reply.
     *
     * @see https://developer.teamwork.com/messagereplies#create_a_message_
     */
    public function createReply($messageId, $params)
    {
        $this->assertValidResourceId($messageId, 'You must specify a valid message ID when creating message replies.');

        /** @var $validator \Illuminate\Validation\Validator */
        $validator = \Validator::make($params, [
            'body'   => 'required',
        ], [
            'body.required'   => '`body` is a required field when creating new message reply.',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequest($validator->getMessageBag()->first());
        }

        return $this->apiClient->post(sprintf('messages/%s/messageReplies.json', $messageId), [
            'messageReply' => $params
        ]);
    }

    /**
     * Get Replies for Message
     *
     * Returns replies for a message when given the message ID.
     *
     * @param $messageId int The message ID.
     * @param $query array Additional options to set.
     * @param $query['page'] int The page number to show, when paging.
     * @param $query['pageSize'] int The number of results to show per page.
     *
     * @see https://developer.teamwork.com/messagereplies#retrieve_replies_
     *
     * @return null|\SquareBit\Dovetail\Api\json
     */
    public function getReplies($messageId, $query = null)
    {
        $this->assertValidResourceId($messageId, 'You must specify a valid message ID when getting message replies.');

        return $this->apiClient->get(sprintf('messages/%s/replies.json', $messageId), $query)->messageReplies;
    }
}