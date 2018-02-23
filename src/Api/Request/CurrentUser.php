<?php

namespace SquareBit\Dovetail\Api\Request;

use SquareBit\Dovetail\Api\Exception\InvalidRequest;

class CurrentUser extends RequestAbstract
{
    /**
     * Get Current User
     *
     * Gets the current user's information.
     */
    public function get()
    {
        return $this->apiClient->get('me.json');
    }

    /**
     * Get Current User Summary
     *
     * Gets the current user's summary for details such as milestones, tasks, events, etc.
     *
     * @param $query array|null An array of GET params to filter the request with.
     */
    public function summary($query = null)
    {
        return $this->apiClient->get('stats.json', $query);
    }

    /**
     * Get User's Status
     *
     * Get the current user's status.
     */
    public function getStatus()
    {
        return $this->apiClient->get('me/status.json');
    }

    /**
     * Update Status
     *
     * Updates the current users status.
     *
     * @param $statusMessage string The status to set.
     * @param $notifyCompany bool When true, notifies the company of the status update. Defaults to false.
     */
    public function createStatus($statusMessage, $notifyCompany = null)
    {
        return $this->apiClient->post('me/status.json', [
            'userstatus' => (object)[
                'status' => $statusMessage,
                'notify' => ($notifyCompany ? 'yes' : 'no')
            ]
        ]);
    }

    /**
     * Update Status
     *
     * Updates the current user's status when given the status ID.
     *
     * @param $statusId int The status ID to update.
     * @param $params array An array of options to set.
     * @param $params['status'] string The status message to set.
     * @param $params['notify'] bool When true, notifies the company of the update. Default to false.
     *
     * @return null|\SquareBit\Dovetail\Api\json
     * @throws InvalidRequest
     */
    public function updateStatus($statusId, $params)
    {
        $this->assertValidResourceId($statusId, 'You must specify a valid status ID when updating a status.');

        /** @var $validator \Illuminate\Validation\Validator */
        $validator = \Validator::make($params, [
            'status'   => 'required|max:160',
        ], [
            'status.required'   => '`status` is a required field when updating a person status.',
            'status.max'   => '`status` for a person cannot be longer than 160 characters.',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequest($validator->getMessageBag()->first());
        }

        // Set notification option
        $notifyTeam = 'no';

        if (isset($params['notify'])
            && ('yes' === $params['notify'] || true === $params['notify'])) {
            $notifyTeam = 'yes';
        }

        return $this->apiClient->put(sprintf('me/status/%s.json', $statusId), [
            'userstatus' => (object)[
                'status' => $params['status'],
                'notify' => $notifyTeam
            ]
        ]);
    }

    /**
     * Delete Status
     *
     * Deletes the current user's status when given the status ID.
     *
     * @param $statusId The status ID to delete.
     */
    public function deleteStatus($statusId)
    {
        $this->assertValidResourceId($statusId, 'You must specify a valid status ID when deleting a status.');

        return $this->apiClient->delete(sprintf('me/status/%s.json', $statusId));
    }
}