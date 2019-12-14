<?php

namespace Fruitware\LaravelSurveyJS\LaravelSurveyJS\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class SurveyResultResource
 *
 * @package Fruitware/LaravelSurveyJS
 */
class SurveyResultResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'            =>  $this->id,
            'json'          =>  $this->json,
            'ip_address'    =>  $this->ip_address,
            'created_at'    =>  $this->created_at->formatLocalized('%A %d %B %Y'),
        ];
    }
}
