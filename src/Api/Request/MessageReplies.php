<?php

namespace SquareBit\Dovetail\Api\Request;

use SquareBit\Dovetail\Api\Exception\InvalidRequest;

class MessageReplies extends RequestAbstract
{

    /**
     * Get A Message Reply
     *
     * Returns a message reply when given a valid ID.
     *
     * @param $messageReplyId int The message reply ID.
     *
     * @see https://developer.teamwork.com/messagereplies#retrieve_a_single
     *
     * @return null|\SquareBit\Dovetail\Api\json
     */
    public function get($messageReplyId)
    {
        $this->assertValidResourceId($messageReplyId, 'You must specify a valid message reply ID when getting a message reply.');

        return $this->apiClient->get(sprintf('messageReplies/%s.json', $messageReplyId))->messageReplies;
    }

    /**
     * Update Message Reply
     *
     * Updates a message reply when given a valid message reply ID and attributes to update.
     *
     * @var $params array An array of parameters for the message reply
     *
     * @see https://developer.teamwork.com/comments#update_message_re
     */
    public function update($messageReplyId, $params)
    {
        $this->assertValidResourceId($messageReplyId, 'You must specify a valid message reply ID when updating a message reply.');

        return $this->apiClient->put(sprintf('messageReplies/%s.json', $messageReplyId), [
            'messagereply' => $params
        ]);
    }

    /**
     * Mark Message Reply as Read
     *
     * Marks a message reply as read for the currently authenticated user.
     *
     * @param $messageReplyId int The message reply ID
     *
     * @see https://developer.teamwork.com/messagereplies#mark_message_repl
     */
    public function markRead($messageReplyId)
    {
        $this->assertValidResourceId($messageReplyId, 'You must specify a valid message reply ID when marking a reply as read.');

        return $this->apiClient->put(sprintf('messageReplies/%s/markread.json', $messageReplyId));
    }

    /**
     * Delete Message Reply
     *
     * Deletes a message reply when given a valid message reply ID.
     *
     * @param $messageReplyId The message reply ID to delete.
     */
    public function delete($messageReplyId)
    {
        $this->assertValidResourceId($messageReplyId, 'You must specify a valid message reply ID when deleting a reply.');

        return $this->apiClient->delete(sprintf('messageReplies/%s.json', $messageReplyId));
    }


    /**
     * Create Message Reply
     *
     * Creates a message reply when given the message ID.
     * HTML may be passed without specifying content type.
     *
     * @param $messageId id A message Id to specify as the parent to reply to.
     * @param $params array An array of parameters for the new message reply.
     *
     * @see https://developer.teamwork.com/messagereplies#create_a_message_
     */
    public function create($messageId, $params)
    {
        $this->assertValidResourceId($messageId, 'You must specify a valid message ID when creating a message reply.');

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
     * Get All Replies for Message
     *
     * Gets all replies for a message when given message ID.
     *
     * @param $messageId int The parent message ID.
     * @param $messageId int The message ID.
     * @param $query array Additional options to set.
     * @param $query['page'] int The page number to show, when paging.
     * @param $query['pageSize'] int The number of results to show per page.
     *
     * @see https://developer.teamwork.com/messagereplies#retrieve_replies_
     *
     * @return null|\SquareBit\Dovetail\Api\json
     * @throws InvalidRequest
     */
    public function allForMessage($messageId, $query = null)
    {
        $this->assertValidResourceId($messageId, 'You must specify a valid message ID when getting message replies.');

        return $this->apiClient->get(sprintf('messages/%s/replies.json', $messageId), $query)->messageReplies;
    }
}