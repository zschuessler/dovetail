<?php

namespace SquareBit\Dovetail\Api\Request;

use SquareBit\Dovetail\Api\Exception\InvalidRequest;

class Links extends RequestAbstract
{
    /**
     * Get All Links
     *
     * Returns all links the current user is associated with.
     *
     * @see https://developer.teamwork.com/links#list_all_links
     *
     * @return null|\SquareBit\Dovetail\Api\json
     */
    public function all()
    {
        return $this->apiClient->get('links.json')->projects;
    }

    /**
     * Get All Links for Project
     *
     * Gets all links for a project when given project ID.
     *
     * @param $projectId int The project ID.
     *
     * @see https://developer.teamwork.com/links#list_links_on_a_p
     */
    public function allForProject($projectId)
    {
        $this->assertValidResourceId($projectId, 'You must specify a valid project ID when getting links for a project.');

        return $this->apiClient->get(sprintf('projects/%s/links.json', $projectId))->project;
    }

    /**
     * Get A Link
     *
     * Returns a link when given a valid ID.
     *
     * @param $linkId int The link ID.
     *
     * @see https://developer.teamwork.com/links#get_a_single_link
     *
     * @return null|\SquareBit\Dovetail\Api\json
     */
    public function get($linkId)
    {
        $this->assertValidResourceId($linkId, 'You must specify a valid link ID when getting a link.');

        return $this->apiClient->get(sprintf('links/%s.json', $linkId))->link;
    }

    /**
     * Create Link
     *
     * Creates a new link when given a data array.
     *
     * @param $options
     *
     * @see https://developer.teamwork.com/links#create_a_single_l
     */
    public function create($projectId, $options)
    {
        $this->assertValidResourceId($projectId, 'You must specify a valid project ID when creating a link.');

        /** @var $validator \Illuminate\Validation\Validator */
        $validator = \Validator::make($options, [
            'code'    => 'required',
        ], [
            'code.required'   => '`code` is a required field when creating new link.',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequest($validator->getMessageBag()->first());
        }

        return $this->apiClient->post(sprintf('projects/%s/links.json', $projectId), [
            'link' => $options
        ]);
    }

    /**
     * Update Link
     *
     * Updates a link when given a valid link ID and link attributes to update.
     *
     * @param $params array An array of parameters for the link
     *
     * @see https://developer.teamwork.com/links#update_a_single_l
     */
    public function update($linkId, $params)
    {
        $this->assertValidResourceId($linkId, 'You must specify a valid link ID when updating a link.');

        return $this->apiClient->put(sprintf('links/%s.json', $linkId), [
            'link' => $params
        ]);
    }

    /**
     * Delete Link
     *
     * Deletes a link when given a valid link ID.
     *
     * @param $messageId The link ID to delete.
     *
     * @see https://developer.teamwork.com/links#delete_a_single_l
     */
    public function delete($linkId)
    {
        $this->assertValidResourceId($linkId, 'You must specify a valid link ID when deleting a link.');

        return $this->apiClient->delete(sprintf('links/%s.json', $linkId));
    }
}