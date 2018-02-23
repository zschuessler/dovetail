<?php

namespace SquareBit\Dovetail\Api\Request;

use SquareBit\Dovetail\Api\Exception\InvalidRequest;

class Notebooks extends RequestAbstract
{
    /**
     * Get All Notebooks
     *
     * Returns all notebooks.
     *
     * @param $options array An array of optional GET params.
     * @param $options['includeContent'] bool Returns HTML content if true. Defaults to false.
     *
     * @return null|\SquareBit\Dovetail\Api\json
     */
    public function all($options = null)
    {
        return $this->apiClient->get('notebooks.json', $options)->projects;
    }

    /**
     * Get All Notebooks for Project
     *
     * Gets all notebooks for the given project ID.
     *
     * @param $projectId int The project ID.
     * @param null $options
     */
    public function allForProject($projectId, $options = null)
    {
        $this->assertValidResourceId($projectId, 'A valid project ID is required when getting notebooks for a project.');

        return $this->apiClient->get(sprintf('projects/%s/notebooks.json', $projectId), $options)->project;
    }

    /**
     * Get All Notebooks for Category
     *
     * Gets all notebooks for the given category ID.
     *
     * @param $categoryId int The category ID.
     * @param null $options
     *
     * @see https://developer.teamwork.com/notebooks#list_notebooks_in
     */
    public function allForCategory($categoryId, $options = null)
    {
        $this->assertValidResourceId($categoryId, 'A valid category ID is required when getting notebooks for a category.');

        return $this->apiClient->get(sprintf('notebookCategories/%s/notebooks.json', $categoryId), $options)->projects;
    }

    /**
     * Get A Notebook
     *
     * Returns a notebook when given a valid ID.
     *
     * @param $notebookId int The notebook ID.
     *
     * @see https://developer.teamwork.com/notebooks#get_a_single_note
     *
     * @return null|\SquareBit\Dovetail\Api\json
     */
    public function get($notebookId)
    {
        $this->assertValidResourceId($notebookId, 'A valid notebook ID is required when getting notebook by ID.');

        return $this->apiClient->get(sprintf('notebooks/%s.json', $notebookId))->notebook;
    }


    /**
     * Create a Notebook
     *
     * Creates a notebook given a valid project ID and data array.
     *
     * @param $projectId int The project ID.
     * @param $options array An array of notebook options.
     *
     * @see https://developer.teamwork.com/notebooks#create_a_single_n
     *
     * @return null|\SquareBit\Dovetail\Api\json
     */
    public function create($projectId, $options)
    {
        $this->assertValidResourceId($projectId, 'You must specify a valid project ID when creating a notebook.');

        /** @var $validator \Illuminate\Validation\Validator */
        $validator = \Validator::make($options, [
            'content'   => 'required',
            'name'      => 'required'
        ], [
            'content.required'   => '`content` is a required field when creating new notebook.',
            'name.required'   => '`name` is a required field when creating new notebook.',
        ]);

        if ($validator->fails()) {
            throw new InvalidRequest($validator->getMessageBag()->first());
        }

        return $this->apiClient->post(sprintf('projects/%s/notebooks.json', $projectId), [
            'notebook' => $options
        ]);
    }

    /**
     * Update Notebook
     *
     * Updates a notebook when given a valid notebook ID and attributes to update.
     *
     * @var $params array An array of parameters for the notebook
     */
    public function update($notebookId, $params)
    {
        $this->assertValidResourceId($notebookId, 'You must specify a valid notebook ID when updating a notebook.');

        return $this->apiClient->put(sprintf('notebooks/%s.json', $notebookId), [
            'notebook' => $params
        ]);
    }


    /**
     * Delete Notebook
     *
     * Deletes a notebook when given a valid notebook ID.
     *
     * @param $notebookId The notebook ID to delete.
     */
    public function delete($notebookId)
    {
        $this->assertValidResourceId($notebookId, 'You must specify a valid notebook ID when deleting a notebook.');

        return $this->apiClient->delete(sprintf('notebooks/%s.json', $notebookId));
    }

    /**
     * Lock a Notebook
     *
     * Marks a notebook as locked when given a valid notebook ID.
     *
     * @param $notebookId The notebook ID
     */
    public function lock($notebookId)
    {
        $this->assertValidResourceId($notebookId, 'You must specify a valid notebook ID when marking a notebook as locked.');

        return $this->apiClient->put(sprintf('notebooks/%s/lock.json', $notebookId));
    }

    /**
     * Unlock a Notebook
     *
     * Marks a notebook as unlocked when given a valid notebook ID.
     *
     * @param $notebookId The notebook ID
     */
    public function unlock($notebookId)
    {
        $this->assertValidResourceId($notebookId, 'You must specify a valid notebook ID when marking a notebook as unlocked.');

        return $this->apiClient->put(sprintf('notebooks/%s/unlock.json', $notebookId));
    }

    /**
     * Copy Notebook to Project
     *
     * Copies the notebook to a project, when given the notebook ID and project ID.
     *
     * @param $notebookId int The notebook ID.
     * @param $projectId int The project ID.
     */
    public function copyToProject($notebookId, $projectId)
    {
        $this->assertValidResourceId($notebookId, 'You must specify a valid notebook ID when copying notebook to a project.');
        $this->assertValidResourceId($projectId, 'You must specify a valid project ID when copying a notebook to a project.');


        $this->apiClient->put(sprintf('notebooks/%s/copy.json', $notebookId), [
            'projectId' => $projectId
        ]);
    }

    /**
     * Move Notebook to Project
     *
     * Moves the notebook to a project, when given the notebook ID and project ID.
     *
     * @param $notebookId int The notebook ID.
     * @param $projectId int The project ID.
     */
    public function moveToProject($notebookId, $projectId)
    {
        $this->assertValidResourceId($notebookId, 'You must specify a valid notebook ID when moving notebook to a project.');
        $this->assertValidResourceId($projectId, 'You must specify a valid project ID when moving a notebook to a project.');


        $this->apiClient->put(sprintf('notebooks/%s/move.json', $notebookId), [
            'projectId' => $projectId
        ]);
    }
}