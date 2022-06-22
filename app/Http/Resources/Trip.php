<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Trip extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title,
            'description' => $this->description,
            'location' => $this->location,
            'price' => $this->price,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date
        ];
    }
}
