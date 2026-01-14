<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CourseDetailResource extends JsonResource
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
            'course_type' => $this->course_type,
            'duration' => $this->duration_minutes,
            'theme_color' => $this->theme_color,
            'thumbnail' => $this->thumbnail,
            'sos_audio' => $this->ssoAudio,
             'progress' => optional(
                $this->progress->first()
            )->progress_percent ?? 0,
            'lessons' => LessonResource::collection($this->lessons),
        ];
    }
}
