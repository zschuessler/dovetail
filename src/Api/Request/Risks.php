<?php

namespace SquareBit\Dovetail\Api\Request;

use SquareBit\Dovetail\Api\Exception\InvalidRequest;

class Risks extends RequestAbstract
{
    /**
     * Get Risk
     *
     * Returns a risk when given a valid risk ID.
     *
     * @return null|\SquareBit\Dovetail\Api\json
     */
    public function get($riskId)
    {
        $this->assertValidResourceId($riskId, 'You must specify a valid risk ID when getting a risk.');

        return $this->apiClient->get(sprintf('risks/%s.json', $riskId))->risk;
    }

    /**
     * Get All Risks
     *
     * Returns all risks.
     *
     * @return null|\SquareBit\Dovetail\Api\json
     */
    public function all()
    {
        return $this->apiClient->get('risks.json')->risks;
    }

    /**
     * All For Project
     *
     * Gets all risks for a given project ID.
     *
     * @param $projectId int The project ID.
     *
     * @see https://developer.teamwork.com/risks#retrieve_all_risk
     *
     * @return null|\SquareBit\Dovetail\Api\json
     */
    public function allForProject($projectId)
    {
        $this->assertValidResourceId($projectId, 'You must specify a valid project ID when getting risks for a project.');

        return $this->apiClient->get(sprintf('projects/%s/risks.json', $projectId))->risks;
    }

    /**
     * Update Risk
     *
     * Updates a risk when given a valid risk ID and risk attributes to update.
     *
     * @var $params array An array of parameters for the risk
     */
    public function update($riskId, $params)
    {
        $this->assertValidResourceId($riskId, 'You must specify a valid risk ID when updating a risk.');

        return $this->apiClient->put(sprintf('risks/%s.json', $riskId), [
            'risk' => $params
        ]);
    }

    /**
     * Delete Risk
     *
     * Deletes a risk when given a valid risk ID.
     *
     * @param $messageId The risk ID to delete.
     */
    public function delete($riskId)
    {
        $this->assertValidResourceId($riskId, 'You must specify a valid risk ID when deleting a risk.');

        return $this->apiClient->delete(sprintf('risks/%s.json', $riskId));
    }
}