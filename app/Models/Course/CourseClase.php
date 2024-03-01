<?php

namespace App\Models\Course;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CourseClase extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        "course_section_id",
        "name",
        "video_link",
        "time",
        "state",
    ];

    public function setCreateAttribute($value) {

        date_default_timezone_set("America/Mexico");
        $this->attributes["created_at"] = Carbon::now();
    }

    public function setUpdateAttribute($value) {

        date_default_timezone_set("America/Mexico");
        $this->attributes["update_at"] = Carbon::now();
    }

    public function course_Section(){
        return $this->belongsTo(CourseSection::class);
    }

    public function files(){
        return $this->hasMany(CourseClaseFile::class, "course_clase_id");
    }
}
