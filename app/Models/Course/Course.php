<?php

namespace App\Models\Course;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Sale\Review;
use App\Models\CoursesStudent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Course extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        "title",
        "subtitle",
        "slug",
        "imagen",
        "categorie_id",
        "sub_categorie_id",
        "user_id",
        "level",
        "idioma",
        "time",
        "description",
        "requirements",
        "who_is_it_for",
        "state",
    ];

    public function setCreatedAtAttribute($value)
    {
        date_default_timezone_set("America/Lima");
        $this->attributes["created_at"] = Carbon::now();
    }

    public function setUpdatedAtAttribute($value)
    {
        date_default_timezone_set("America/Lima");
        $this->attributes["updated_at"] = Carbon::now();
    }

    public function instructor()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function categorie()
    {
        return $this->belongsTo(Categorie::class);
    }

    public function sub_categorie()
    {
        return $this->belongsTo(Categorie::class);
    }

    public function sections()
    {
        return $this->hasMany(CourseSection::class);
    }

    public function courses_students()
    {
        return $this->hasMany(CoursesStudent::class);
    }
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }


    // public function discount_courses()
    // {
    //     return $this->hasMany(DiscountCourse::class);
    // }

    // public function getDiscountCAttribute()
    // {
    //     date_default_timezone_set("America/Lima");
    //     $discount = null;
    //     foreach ($this->discount_courses as $key => $discount_course) {
    //        if($discount_course->discount->type_campaing == 1 &&  $discount_course->discount->state == 1){
    //         if(Carbon::now()->between($discount_course->discount->start_date,Carbon::parse($discount_course->discount->end_date)->addDays(1))){
    //             // EXISTE UNA CAMPAÑA DE DESCUENTO CON EL CURSO
    //             $discount = $discount_course->discount;
    //             break;
    //         }
    //        }
    //     }
    //     return $discount;
    // }

    // public function getDiscountCTAttribute()
    // {
    //     date_default_timezone_set("America/Lima");
    //     $discount = null;
    //     foreach ($this->categorie->discount_categories as $key => $discount_categorie) {
    //        if($discount_categorie->discount->type_campaing == 1 && $discount_categorie->discount->state == 1){
    //         if(Carbon::now()->between($discount_categorie->discount->start_date,Carbon::parse($discount_categorie->discount->end_date)->addDays(1))){
    //             // EXISTE UNA CAMPAÑA DE DESCUENTO CON EL CURSO
    //             $discount = $discount_categorie->discount;
    //             break;
    //         }
    //        }
    //     }
    //     return $discount;
    // }

    public function getFilesCountAttribute()
    {
        $files_count = 0;

        foreach ($this->sections as $keyS => $section) {
            foreach ($section->clases as $keyC => $clase) {
                $files_count += $clase->files->count();
            }
        }

        return $files_count;
    }
function AddTimes($horas)
    {
        $total = 0;
    
        // Verificar si el array no está vacío
        if (!empty($horas) && is_array($horas)) {
            foreach ($horas as $h) {
                // Verificar si la cadena tiene el formato esperado
                if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $h)) {
                    $parts = explode(":", $h);
                    // Verificar si hay suficientes elementos en el array después de la explosión
                    if (count($parts) === 3) {
                        $total += $parts[2] + $parts[1] * 60 + $parts[0] * 3600;
                    } else {
                        // Manejar el caso en el que no hay suficientes partes en la cadena
                        return "Formato de hora no válido";
                    }
                } else {
                    // Manejar el caso en el que la cadena no tiene el formato esperado
                    return "Formato de hora no válido";
                }
            }
    
            $hours = floor($total / 3600);
            $minutes = floor(($total / 60) % 60);
            $seconds = $total % 60;
    
            return $hours . " hrs " . $minutes . " mins";
        } else {
            // Manejar el caso en el que el array está vacío o no es válido
            return "Array de horas no válido";
        }
    }
    

    public function getCountClassAttribute()
    {
        $num = 0;

        foreach ($this->sections as $key => $section) {
            $num += $section->clases->count();
        }

        return $num;
    }

    public function getTimeCourseAttribute()
    {
       $times = [];
       foreach ($this->sections as $keyS => $section) {
        foreach ($section->clases as $keyC => $clase) {
            array_push($times,$clase->time);
        }
       }
       return $this->AddTimes($times);
    }

    function scopeFilterAdvance($query,$search,$state)
    {
        if($search){
            $query->where("title","like","%".$search."%");
        }
        if($state){
            $query->where("state",$state);
        }
        
        return $query;
    }
    function scopeFilterAdvanceEcommerce($query,$search,$selected_categories = [],
    $idiomas_selected = [],$levels_selected = [],
    $courses_a = [],$rating_selected = 0)
        {
        if($search){
        $query->where("title","like","%".$search."%");
        }
        if(sizeof($selected_categories) > 0){
        $query->whereIn("categorie_id",$selected_categories);
        }
        if(sizeof($idiomas_selected) > 0){
        $query->whereIn("idioma",$idiomas_selected);
        }
        if(sizeof($levels_selected) > 0){
        $query->whereIn("level",$levels_selected);
        }
        if($courses_a || $rating_selected){
        $query->whereIn("id",$courses_a);
        }
        return $query;
}



    public function getCountStudentsAttribute()
    {
        return $this->courses_students->count();
    }

    public function getCountReviewsAttribute()
    {
        return $this->reviews->count();
    }

    public function getAvgReviewsAttribute()
    {
        return $this->reviews->avg("rating");
    }
    

    
}
