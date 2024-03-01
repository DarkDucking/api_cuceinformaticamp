<?php

namespace App\Http\Controllers\Admin\Course;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Course\Course;
use App\Models\Course\Categorie;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\Course\CourseGResource;
use App\Http\Resources\Course\CourseGCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CourseGController extends Controller
{
       /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $userId = auth('api')->user()->id;
        error_log($userId);
        $type_user = auth('api')->user()->type_user;
        $search = $request->search;
        $state = $request->state;

        $query = Course::orderBy("id", "desc");

        if ($type_user == 1) {
            // Si el tipo de usuario es 1, filtrar por user_id
            $query->where('user_id', $userId);
        }
        //filterAdvance($search,$state)->
        $courses = $query->get();
        

        return response()->json([
            "courses" => CourseGCollection::make($courses),

        ]);
    }

    public function config(){
        $categories = Categorie::where("categorie_id",NULL)->orderBy("id","desc")->get();
        $subcategories = Categorie::where("categorie_id","<>",NULL)->orderBy("id","desc")->get();

        $instructores = User::where("is_instructor",1)->orderBy("id","desc")->get();
        return response()->json([
            "categories" => $categories,
            "subcategories" => $subcategories,

            "instructores" => $instructores->map(function($user){
                return[
                    "id" => $user->id,
                    "full_name" => $user->name.' '.$user->surname,
                ];
            }),
        ]);
    }

    public function totalClasesPorCurso(){
        $cursos = DB::table('courses')
            ->select('categories.name as category', DB::raw('COUNT(course_clases.id) as total_clases'))
            ->leftJoin('course_sections', 'courses.id', '=', 'course_sections.course_id')
            ->leftJoin('course_clases', 'course_sections.id', '=', 'course_clases.course_section_id')
            ->leftJoin('categories', 'courses.categorie_id', '=', 'categories.id')
            ->whereNull('courses.deleted_at') // Filtrar cursos que no han sido eliminados
            ->groupBy('categories.name')
            ->get();
    
        return response()->json($cursos);
    }
    
    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $is_exits = Course::where("title",$request->title)->first();
        if($is_exits){
            return response()->json(["message" => 403, "message_text" => "Ya existe un curso con ese título"]);
        }

        if($request->hasFile("portada")){
            $path = Storage::putFile("courses",$request->file("portada"));
            $request->request->add(["imagen" => $path]);

        }
        $request->request->add(["slug" => Str::slug($request->title)]);
        $request->request->add(["requirements" => json_encode(explode(",",$request->requirements))]);
        $request->request->add(["who_is_it_for" => json_encode(explode(",",$request->who_is_it_for))]);
        $course = Course::create($request->all());

        return response()->json(["message" => 200]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $course = Course::findOrFail($id);

        return response()->json([
            "course" => CourseGResource::make($course),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $is_exits = Course::where("id","<>",$id)->where("title",$request->title)->first();
        if($is_exits){
            return response()->json(["message" => 403, "message_text" => "Ya existe un curso con ese título"]);
        }

        $course = Course::findOrFail($id);
        if($request->hasFile("portada")){
            if($course->imagen){
                Storage::delete($course->imagen);
            }
            $path = Storage::putFile("courses",$request->file("portada"));
            $request->request->add(["imagen" => $path]);

        }
        $request->request->add(["slug" => Str::slug($request->title)]);
        $request->request->add(["requirements" => json_encode(explode(",",$request->requirements))]);
        $request->request->add(["who_is_it_for" => json_encode(explode(",",$request->who_is_it_for))]);
        $course->update($request->all());
        return response()->json(["course" => CourseGResource::make($course)]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $course = Course::findOrFail($id);
        $course->delete();

        return response()->json(["message" => 200]);
    }
}
