<?php

namespace SquareBit\Dovetail\Api\Request;

use SquareBit\Dovetail\Api\Exception\InvalidRequest;

class People extends RequestAbstract
{
    /**
     * Get Current User
     *
     * Gets the current user's information.
     *
     * @see https://developer.teamwork.com/people#get_current_user_
     */
    public function current()
    {
        return $this->apiClient->get('me.json')->person;
    }

    /**
     * Get Current User Summary
     *
     * Gets the current user's summary for details such as milestones, tasks, events, etc.
     *
     * @param $query array|null An array of GET params to filter the request with.
     */
    public function getCurrentUserSummary($query = null)
    {
        return $this->apiClient->get('stats.json', $query);
    }

    /**
     * Get All People
     *
     * Returns all people, up to 100 records at a time.
     *
     * @param $options array An array of optional GET params.
     * @param $options['emailaddress'] string Checks if an email address exists when present.
     * @param $options['fullprofile'] bool When true, also returns private notes on users.
     * @param $options['returnProjectIds'] bool When true, returns all project IDs user is part of.
     *
     * @see https://developer.teamwork.com/people#get_people
     *
     * @return null|\SquareBit\Dovetail\Api\json
     */
    public function all($options = null)
    {
        return $this->apiClient->get('people.json', $options)->people;
    }

    /**
     * Get All People for Project
     *
     * Gets all people for a given project ID.
     *
     * @param $projectId int The project ID.
     *
     * @see https://developer.teamwork.com/people#get_all_people_(w
     *
     * @return null|\SquareBit\Dovetail\Api\json
     */
    public function allForProject($projectId)
    {
        $this->assertValidResourceId($projectId, 'You must specify a valid project ID when getting all people on a project.');

        return $this->apiClient->get(sprintf('projects/%s/people.json', $projectId))->people;
    }

    /**
     * Get All People for Company
     *
     * Gets all people for a given company ID.
     *
     * @param $companyId int The company ID.
     *
     * @see https://developer.teamwork.com/people#get_people_(withi
     *
     * @throws InvalidRequest
     *
     * @return array An array of People objects.
     */
    public function allForCompany($companyId)
    {
        $this->assertValidResourceId($companyId, 'You must specify a valid company ID when getting all people for a company.');

        return $this->apiClient->get(sprintf('companies/%s/people.json', $companyId))->people;
    }

    /**
     * Get A Person
     *
     * Returns a person when given a valid ID.
     *
     * @param $personId int The person ID.
     *
     * @see https://developer.teamwork.com/people#retrieve_a_specif
     *
     * @return null|\SquareBit\Dovetail\Api\json
     */
    public function get($personId)
    {
        $this->assertValidResourceId($personId, 'You must specify a valid person ID when getting a person by ID.');

        return $this->apiClient->get(sprintf('people/%s.json', $personId))->person;
    }

    /**
     * Update Person
     *
     * Updates a person when given a valid person ID and attributes to update.
     *
     * @var $params array An array of parameters for the person
     *
     * @see https://developer.teamwork.com/people#edit_user
     */
    public function update($personId, $params)
    {
        $this->assertValidResourceId($personId, 'You must specify a valid person ID when updating a person.');

        return $this->apiClient->put(sprintf('people/%s.json', $personId), [
            'person' => $params
        ]);
    }


    /**
     * Delete Person
     *
     * Deletes a person when given a valid person ID.
     *
     * @param $personId The person ID to delete.
     *
     * @see https://developer.teamwork.com/people#delete_user
     */
    public function delete($personId)
    {
        $this->assertValidResourceId($personId, 'You must specify a valid person ID when deleting a person.');

        return $this->apiClient->delete(sprintf('people/%s.json', $personId));
    }

    /**
     * Create New Person
     *
     * Creates a new person. On success, returns an object with the person ID.
     *
     * @var $params array An array of parameters for the new person.
     *
     * @see https://developer.teamwork.com/people#add_a_new_user
     */
    public function create($params)
    {
        /** @var $validator \Illuminate\Validation\Validator */
        $validator = \Validator::make($params, [
            'first-name'    => 'required',
            'last-name'     => 'required',
            'email-address' => 'required',
            'user-name'     => 'required'
        ], [
            'first-name.required'    => '`first-name` is a required field when creating new person.',
            'last-name.required'     => '`last-name` is a required field when creating new person.',
            'email-address.required' => '`email-address` is a required field when creating new person.',
            'user-name.required'     => '`user-name` is a required field when creating new person.'
        ]);

        if ($validator->fails()) {
            throw new InvalidRequest($validator->getMessageBag()->first());
        }

        return $this->apiClient->post('people.json', [
            'person' => $params
        ]);
    }

    /**
     * Get API Keys
     *
     * Gets all API keys for all people in the Teamwork account.
     *
     * @see https://developer.teamwork.com/people#retrieve_a_api_ke
     *
     * @return null|\SquareBit\Dovetail\Api\json
     */
    public function getApiKeys()
    {
        return $this->apiClient->get('people/APIKeys.json')->people;
    }

    /**
     * Unassign All Tasks
     *
     * Unassigns all tasks for the given person ID.
     *
     * @param $personId
     */
    public function unassignAllTasks($personId)
    {
        $this->assertValidResourceId($personId, 'You must specify a valid person ID when unassigning al ltasks.');

        return $this->apiClient->put(sprintf('people/%s.json', $personId), [
            'person' => (object)[
                'unassignFromAll' => '1'
            ]
        ]);
    }

    /**
     * Create Status
     *
     * Creates a status when given the person ID and data array.
     *
     * @param $personId The person ID.
     * @param $options array An array of status options.
     */
    public function createStatus($personId, $options)
    {
        $this->assertValidResourceId($personId, 'You must specify a valid person ID when creating a status.');

        /** @var $validator \Illuminate\Validation\Validator */
        $validator = \Validator::make($options, [
            'status'    => 'required|max:160'
        ], [
            'status.required'    => '`status` is a required field when creating new person status.',
            'status.max'   => '`status` for a person cannot be longer than 160 characters.',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequest($validator->getMessageBag()->first());
        }

        // Normalize the `notify` option to be a string
        if (!isset($options['notify']) || !$options['notify']) {
            $options['notify'] = 'no';
        } else if (isset($options['notify']) && true === $options['notify']) {
            $options['notify'] = 'yes';
        }

        return $this->apiClient->post(sprintf('people/%s/status.json', $personId), [
            'userstatus' => (object)[
                'status' => $options['status'],
                'notify' => $options['notify']
            ]
        ]);
    }

    /**
     * Get Person Status
     *
     * Gets a persons status when given person ID.
     *
     * @param $personId
     *
     * @see https://developer.teamwork.com/people-status#retrieve_a_person
     */
    public function getStatus($personId)
    {
        $this->assertValidResourceId($personId, 'You must specify a valid person ID when getting a person status.');

        return $this->apiClient->get(sprintf('people/%s/status.json', $personId))->userStatus;
    }

    /**
     * Get All Statuses
     *
     * Returns the latest status for all people in the parent company.
     *
     * @see https://developer.teamwork.com/people-status#retrieve_everybod
     */
    public function allStatuses()
    {
        return $this->apiClient->get('people/status.json')->userStatuses;
    }

    /**
     * Update Status
     *
     * Modifies a person's status when given person ID and data array.
     * This edits a given status, see the `createStatus()` method if you need to make a new status.
     *
     * @param $personId
     * @param $options
     *
     * @see https://developer.teamwork.com/people-status#update_status
     *
     * @return null|\SquareBit\Dovetail\Api\json
     * @throws InvalidRequest
     */
    public function updateStatus($statusId, $options)
    {
        $this->assertValidResourceId($statusId, 'You must specify a valid status ID when modifying a person status.');

        /** @var $validator \Illuminate\Validation\Validator */
        $validator = \Validator::make($options, [
            'status'   => 'required|max:160',
        ], [
            'status.required'   => '`status` is a required field when modifying a person status.',
            'status.max'        => '`status` for a person cannot be longer than 160 characters.',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequest($validator->getMessageBag()->first());
        }

        // Normalize the `notify` option to be a string
        if (!isset($options['notify']) || !$options['notify']) {
            $options['notify'] = 'no';
        } else if (isset($options['notify']) && true === $options['notify']) {
            $options['notify'] = 'yes';
        }

        return $this->apiClient->put(sprintf('people/status/%s.json', $statusId), [
            'userstatus' => (object)[
                'status' => $options['status'],
                'notify' => $options['notify']
            ]
        ]);
    }

    /**
     * Delete Status
     *
     * Deletes a person status when given status ID.
     *
     * @param $statusId
     *
     * @see https://developer.teamwork.com/people-status#delete_status
     *
     */
    public function deleteStatus($statusId)
    {
        $this->assertValidResourceId($statusId, 'You must specify a valid status when deleting a person status.');

        return $this->apiClient->delete(sprintf('people/status/%s.json', $statusId));
    }
}