<?php

namespace SquareBit\Dovetail\Api\Request;

use SquareBit\Dovetail\Api\Exception\InvalidRequest;

class Tags extends RequestAbstract
{
    /**
     * @var array An array of valid resource types which may have tags.
     */
    public $tagTypes = [
        'companies', 'files', 'messages', 'milestones', 'notebooks',
        'projects', 'tasklists', 'tasks', 'timelogs', 'users', 'links'
    ];

    /**
     * @var array An array of valid hex colors tags may be.
     */
    public $tagColors = [
        '#d84640', '#f78234', '#f4bd38', '#b1da34', '#53c944', '#37ced0',
        '#2f8de4', '#9b7cdb', '#f47fbe', '#a6a6a6', '#4d4d4d', '#9e6957'
    ];

    /**
     * @var array array An array of named colors, used to translate to hex colors.
     */
    public $tagColorNames = [
        'red' => '#d84640',
        'red-orange' => '#f78234',
        'orange' => '#f4bd38',
        'yellow-green' => '#b1da34',
        'green' => '#53c944',
        'cyan' => '#37ced0',
        'blue' => '#2f8de4',
        'purple' => '#9b7cdb',
        'pink' => '#f47fbe',
        'gray' => '#a6a6a6',
        'grey' => '#a6a6a6',
        'slate' => '#4d4d4d',
        'brown' => '#9e6957'
    ];

    /**
     * Assert Resource Type is Valid
     *
     * Asserts the given tag type is in the array of valid options.
     *
     * @param $tagType string The tag color.
     *
     * @throws InvalidRequest
     */
    public function assertResourceTypeIsValid($tagType)
    {
        if (!in_array($tagType, $this->tagTypes)) {
            throw new InvalidRequest('The tag type specified is invalid: ' . $tagType);
        }
    }

    /**
     * Translate Tag Color Name
     *
     * Returns either the translated color name, or the current already-translated value.
     *
     * @param $tagColor string The tag color.
     *
     * @return mixed
     */
    public function translateTagColorName($tagColor)
    {
        if (in_array($tagColor, array_keys($this->tagColorNames))) {
            return $this->tagColorNames[$tagColor];
        }

        return $tagColor;
    }

    /**
     * Get Tag
     *
     * Returns a tag when given a valid tag ID.
     *
     * @param $tagId int The tag ID to retrieve.
     *
     * @see https://developer.teamwork.com/tags#get_a_single_tag
     *
     * @return null|\SquareBit\Dovetail\Api\json
     */
    public function get($tagId)
    {
        $this->assertValidResourceId($tagId, 'You must specify a valid tag ID when getting a tag.');

        return $this->apiClient->get(sprintf('tags/%s.json', $tagId))->tag;
    }

    /**
     * Get All Tags
     *
     * Returns all tags.
     *
     * @see https://developer.teamwork.com/tags#list_all_tags
     *
     * @return null|\SquareBit\Dovetail\Api\json
     */
    public function all()
    {
        return $this->apiClient->get('tags.json')->tags;
    }

    /**
     * Get All Tags by Tag Type
     *
     * Gets all tags when given a valid tag type. See the $tagTypes property for valid options.
     *
     * @param $tagType
     *
     * @see https://developer.teamwork.com/tags#list_all_tags_for
     */
    public function allForTagType($tagType)
    {
        $this->assertResourceTypeIsValid($tagType);

        return $this->apiClient->get(sprintf('%s/tags.json', $tagType))->tags;
    }

    /**
     * Create Tag
     *
     * Creates a tag when given a name and color.
     *
     * @param $optionsArray
     *
     * @see https://developer.teamwork.com/tags#create_a_single_t
     *
     * @return null|\SquareBit\Dovetail\Api\json
     */
    public function create($options)
    {
        if (isset($options['color'])) {
            $options['color'] = $this->translateTagColorName($options['color']);
        }

        /** @var $validator \Illuminate\Validation\Validator */
        $validator = \Validator::make($options, [
            'name'   => 'required',
            'color' => 'required|in:' . implode(',', $this->tagColors)
        ], [
            'name.required'   => '`name` is a required field when creating a tag.',
            'color.required'  => '`color` is a required field when creating a tag.',
            'color.in'  => '`color` must be one of the valid hex colors. See $tagColors property.'
        ]);

        if ($validator->fails()) {
            throw new InvalidRequest($validator->getMessageBag()->first());
        }

        return $this->apiClient->post('tags.json', [
            'tag' => (object)[
                'name'  => $options['name'],
                'color' => $options['color']
            ]
        ]);
    }

    /**
     * Update Tag
     *
     * Updates a tag when given a valid tag ID and tag attributes to update.
     *
     * @param $options array An array of parameters for the tag
     *
     * @see https://developer.teamwork.com/tags#update_a_single_t
     */
    public function update($tagId, $options)
    {
        $this->assertValidResourceId($tagId, 'You must specify a valid tag ID when updating a tag.');

        return $this->apiClient->put(sprintf('tags/%s.json', $tagId), [
            'tag' => $options
        ]);
    }

    public function updateAllForType($tagType, $options)
    {

    }

    /**
     * Delete Tag
     *
     * Deletes a tag when given a valid tag ID.
     *
     * @param $messageId The tag ID to delete.
     *
     * @see https://developer.teamwork.com/tags#delete_a_single_t
     */
    public function delete($tagId)
    {
        $this->assertValidResourceId($tagId, 'You must specify a valid tag ID when deleting a tag.');

        return $this->apiClient->delete(sprintf('tags/%s.json', $tagId));
    }
}