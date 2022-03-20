<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'name' => $this->name,
            $this->mergeWhen(auth()->check() && auth()->id() === $this->id, [
                'email' => $this->email,
            ]),
            'photo_url' => $this->photo_url,
            'location' => $this->location,
            'create_dates' => [
                'created_at_human' => $this->created_at->diffForHumans()
            ],
            'designs' => DesignResource::collection($this->whenLoaded('designs')),
            'tagline' => $this->tagline,
            'about' => $this->about,
            'available_to_hire' => $this->available_to_hire,
            'formatted_address' => $this->formatted_address,
            'live_designs' => $this->getLiveDesigns(),
            'specialty' => $this->specialty
        ];
    }
}
