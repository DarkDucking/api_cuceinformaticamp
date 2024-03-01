<?php

namespace App\Http\Resources\Course\Clases;

use Illuminate\Http\Resources\Json\JsonResource;

class CourseClaseResource extends JsonResource
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
            "id" => $this->resource->id,
            "course_section_id" => $this->resource->course_section_id,
            "name" => $this->resource->name,
            "description" => $this->resource->description,
            "video_link" => $this->resource->video_link,
            "time" => $this->resource->time,
            "state" => $this->resource->state,
            "files" => $this->resource->files->map(function($file){
                return [
                    "id" => $file->id,
                    "course_clase_id" => $file->course_clase_id,
                    "name_file" => $file->name_file,
                    "size" => $file->size,
                    "time" => $file->time,
                    "resolution" => $file->resolution,
                    "file" => env("APP_URL")."storage/".$file->file,
                    "type" => $file->type,
                ];
            }),
        ];
    }
}
