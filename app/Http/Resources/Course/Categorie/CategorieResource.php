<?php

namespace App\Http\Resources\Course\Categorie;

use Illuminate\Http\Resources\Json\JsonResource;

class CategorieResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        //CATEGORIA
         // 1 DESARROLLO - IMAGEN
        //SUBCATEGORIA
        // 1 DESARROLLO WEB - NULL
        return [
            "id" => $this->resource->id,
            "name" =>$this->resource->name,
            "imagen" => $this->resource->imagen ? env("APP_URL")."storage/".$this->resource->imagen : NULL,
            "categorie_id" =>$this->resource->categorie_id,
            "categorie" =>$this->resource->father ? [
                "name" => $this->resource->father->name,
                "imagen" => $this->resource->father->imagen ? env("APP_URL")."storage/".$this->resource->father->imagen : NULL,
            ] : NULL,
            "state" =>$this->resource->state ?? 1,
        ];
    }
}
