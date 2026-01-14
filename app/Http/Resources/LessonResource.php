<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LessonResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'lesson_type' => $this->lesson_type,
            'duration' => $this->duration_minutes,
            'is_completed' => (bool) optional(
                $this->progress->first()
            )->is_completed,
            'media' => new LessonMediaResource($this->media),

        ];
    }
}
