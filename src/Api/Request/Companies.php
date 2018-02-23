<?php

namespace SquareBit\Dovetail\Api\Request;

use SquareBit\Dovetail\Api\Exception\InvalidRequest;

class Companies extends RequestAbstract
{
    /**
     * Get All Companies
     *
     * Request:
     * GET /companies.json
     *
     * Returns all companies.
     *
     * @param $options array An array of optional GET params.
     * @param $options['page'] int The page number to show, when paging.
     * @param $options['pageSize'] int The number of results to show per page.
     *
     * @return null|\SquareBit\Dovetail\Api\json
     */
    public function all($options = null)
    {
        return $this->apiClient->get('companies.json', $options)->companies;
    }

    /**
     * Get All Companies for Project
     *
     * Returns all companies for a project, when given a project ID.
     *
     * @param $projectId int The project ID.
     * @return null|\SquareBit\Dovetail\Api\json
     * @throws InvalidRequest
     *
     * @see https://developer.teamwork.com/companies#retrieving_compan
     */
    public function allForProject($projectId)
    {
        $this->assertValidResourceId($projectId, 'You must pass a valid project ID when getting companies for a project.');

        return $this->apiClient->get(sprintf('projects/%s/companies.json', $projectId))->companies;
    }

    /**
     * Get A Company
     *
     * Request:
     * GET /companies/{id}.json
     *
     * Returns a company when given a valid ID.
     *
     * @param $companyId int The company ID.
     *
     * @return null|\SquareBit\Dovetail\Api\json
     */
    public function get($companyId)
    {
        $this->assertValidResourceId($companyId, 'You must specify a valid company ID when getting a company.');

        return $this->apiClient->get(sprintf('companies/%s.json', $companyId))->company;
    }

    /**
     * Create New Company
     *
     * Creates a new company. On success, returns an object with the company ID.
     *
     * @param $params array An array of parameters for the new company.
     *
     * @see https://developer.teamwork.com/companies#create_company
     */
    public function create($params)
    {
        /** @var $validator \Illuminate\Validation\Validator */
        $validator = \Validator::make($params, [
            'name'   => 'required',
        ], [
            'name.required'   => '`name` is a required field when creating new company.',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequest($validator->getMessageBag()->first());
        }

        return $this->apiClient->post('companies.json', [
            'company' => $params
        ]);
    }

    /**
     * Update Company
     *
     * Updates a company when given a valid company ID and company attributes to update.
     *
     * @param $params array An array of parameters for the company
     *
     * @see https://developer.teamwork.com/comments#update_company
     */
    public function update($companyId, $params)
    {
        $this->assertValidResourceId($companyId, 'You must specify a valid company ID when updating a company.');

        return $this->apiClient->put(sprintf('companies/%s.json', $companyId), [
            'company' => $params
        ]);
    }


    /**
     * Delete Company by ID
     *
     * Deletes a company when given a valid company ID.
     *
     * @param $companyId The company ID to delete.
     */
    public function delete($companyId)
    {
        $this->assertValidResourceId($companyId, 'You must specify a valid company ID when deleting a company.');

        return $this->apiClient->delete(sprintf('companies/%s.json', $companyId));
    }
}