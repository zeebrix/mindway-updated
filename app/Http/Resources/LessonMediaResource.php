<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LessonMediaResource extends JsonResource
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
            'audio_url' => $this->audio_url,
            'video_url' => $this->video_url,
            'article_text' => $this->article_text,
            'thumbnail' => $this->thumbnail,
            'host_image' => $this->host_image
                ? asset('storage/' . $this->host_image)
                : asset('images/avatar-default.svg'),
            'host_name' => $this->host_name,
            'author_name' => $this->author_name,
        ];
    }
}
