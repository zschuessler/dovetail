<?php

namespace SquareBit\Dovetail\Api\Request;

use SquareBit\Dovetail\Api\Exception\InvalidRequest;

class Workload extends RequestAbstract
{
    /**
     * Get Workload
     *
     * Returns a workload report when given a valid start and end date.
     *
     * @param array $options An array of GET params to filter the request by.
     * @param $options['startDate'] string|int A date in format 'YYYYMMDD'
     * @param $options['endDate'] string|int A date in format 'YYYYMMDD'
     *
     * @return null|\SquareBit\Dovetail\Api\json
     * @throws InvalidRequest
     */
    public function get($options = [])
    {
        /** @var $validator \Illuminate\Validation\Validator */
        $validator = \Validator::make($options, [
            'startDate' => 'required',
            'endDate'   => 'required'
        ], [
            'startDate.required' => '`startDate` is required when getting workload report.',
            'endDate.required' => '`endDate` is required when getting workload report.'
        ]);

        if ($validator->fails()) {
            throw new InvalidRequest($validator->getMessageBag()->first());
        }

        return $this->apiClient->get('workload.json', $options);
    }
}