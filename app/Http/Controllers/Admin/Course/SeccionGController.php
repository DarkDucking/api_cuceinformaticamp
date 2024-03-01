<?php

namespace App\Http\Controllers\Admin\Course;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Course\CourseSection;

class SeccionGController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sections = CourseSection::where("course_id",$request->course_id)->orderBy("id","asc")->get();

        return response()->json(["sections" => $sections]);
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
        $section = CourseSection::create($request->all());

        return response()->json(["section" => $section]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        $section = CourseSection::findOrFail($id);
        $section->update($request->all());

        return response()->json(["section" => $section]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $section = CourseSection::findOrFail($id);
        // MUY IMPORTANTE
        if($section->clases->count() > 0){
            return response()->json(["message" => 403, "message_text" => "NO PUEDES ELIMINAR ESTA SECCIÓN PORQUE TIENE CLASES DENTRO"]);
        }
        $section->delete();

        return response()->json(["message" => 200]);
    }
}