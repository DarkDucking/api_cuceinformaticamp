<?php

namespace App\Http\Resources\Ecommerce\LandingCourse;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class LandingCourseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $discount_g = null;
        if($this->resource->discount_c && $this->resource->discount_c_t){
            $discount_g = $this->resource->discount_c_t;
        }else{
            if($this->resource->discount_c && !$this->resource->discount_c_t){
                $discount_g = $this->resource->discount_c;
            }else{
                if(!$this->resource->discount_c && $this->resource->discount_c_t){
                    $discount_g = $this->resource->discount_c_t;
                }
            }
        }
        return [
            "id" => $this->resource->id,
            "title" => $this->resource->title,
            "subtitle" => $this->resource->subtitle,
            "categorie_id" => $this->resource->categorie_id,
            "categorie" => [
                "id" => $this->resource->categorie->id,
                "name" => $this->resource->categorie->name
            ],
            "sub_categorie_id" => $this->resource->sub_categorie_id,
            "sub_categorie" => [
                "id" => $this->resource->sub_categorie->id,
                "name" => $this->resource->sub_categorie->name
            ],
            "level" => $this->resource->level,
            "idioma" => $this->resource->idioma,
            "video_link" => $this->resource->video_link,
            "time" => $this->resource->time,
            "imagen" => env("APP_URL")."storage/".$this->resource->imagen,
            "precio_usd" => $this->resource->precio_usd,
            "precio_pen" => $this->resource->precio_pen,
            "count_class" => $this->resource->count_class,
            "time_course" => $this->resource->time_course,
            "files_count" => $this->resource->files_count,
            "count_students" => $this->resource->count_students,
            "avg_reviews" => $this->resource->avg_reviews ? round($this->resource->avg_reviews,2): 0,
            "count_reviews" => $this->resource->count_reviews,
            
            "discount_g" => $discount_g,
            "discount_date" => $discount_g ? Carbon::parse($discount_g->end_date)->format("d/m") : NULL,
            "description" => $this->resource->description,
            "requirements" => json_decode($this->resource->requirements),
            "who_is_it_for" => json_decode($this->resource->who_is_it_for),
            "instructor" => $this->resource->instructor ? [
                "id" => $this->resource->instructor->id,
                "full_name" => $this->resource->instructor->name. ' '. $this->resource->instructor->surname,
                "avatar" => env("APP_URL")."storage/".$this->resource->instructor->avatar,
                "profesion" => $this->resource->instructor->profesion,
                "courses_count"  => $this->resource->instructor->courses_count,
                "description" => $this->resource->instructor->description,
                "avg_reviews" => round($this->resource->instructor->avg_reviews,2),
               
                "count_reviews" => $this->resource->instructor->count_reviews,
                "count_students" => $this->resource->instructor->count_students,
                
                ] : NULL,
            // MALLA CURRICULAR
            "malla" => $this->resource->sections->map(function($section){
                return [
                    "id" => $section->id,
                    "name" => $section->name,
                    "time_section" => $section->time_section,
                    "clases" => $section->clases->map(function($clase) {
                        return [
                            "id" => $clase->id,
                            "name" => $clase->name,
                            "time_clase" => $clase->time_clase,
                            "video_link" => $clase->video_link,
                            "files" => $clase->files->map(function($file) {
                                return [
                                    "name" => $file->name_file,
                                    "url" => env("APP_URL")."storage/".$file->file,
                                    "size" => $file->size,
                                ];
                            })
                        ];
                    })
                ];
            }),
            "updated_at" => $this->resource->updated_at->format("m/Y"),
            "reviews" => $this->resource->reviews->map(function($review){
                return [
                    "message" => $review->message,
                    "rating" => $review->rating,
                    "user" => [
                        "full_name" =>  $review->user->name.' '.$review->user->surname,
                        "avatar" => env("APP_URL")."storage/".$review->user->avatar,
                    ],
                ];
            })
        ];
    }
}
