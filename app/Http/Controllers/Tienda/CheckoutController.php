<?php

namespace App\Http\Controllers\Tienda;

use App\Mail\SaleMail;
use App\Models\Sale\Cart;
use App\Models\Sale\Sale;
use Illuminate\Http\Request;
use App\Models\CoursesStudent;
use App\Models\Sale\SaleDetail;
use App\Models\Course\Categorie;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Ecommerce\Sale\SaleCollection;

class CheckoutController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $coursesStudent = CoursesStudent::all();
        $totalcoursesStudentCount = $coursesStudent->count();

        return response()->json([
            "sales" => $coursesStudent,
            "totalSalesCount" => $totalcoursesStudentCount,
        ]);
    }

    public function indexCoursesStudent()
    {
        $coursesStudent = CoursesStudent::all();
        return response()->json(['coursesStudent' => $coursesStudent]);
    }

    public function consultaAvanzada() {
        $result = Categorie::select('categories.name as category', DB::raw('count(courses_students.id) as total_students'))
            ->leftJoin('courses', 'categories.id', '=', 'courses.categorie_id')
            ->leftJoin('courses_students', 'courses.id', '=', 'courses_students.course_id')
            ->whereNull('categories.categorie_id') // Agrega esta línea para filtrar por categorie_id null
            ->groupBy('category')
            ->orderBy('total_students')
            ->get();
    
        return response()->json($result);
    }

    public function categoriaMenosConsultada(){
        $result = Categorie::select('categories.name as category', DB::raw('count(courses_students.id) as total_students'))
        ->leftJoin('courses', 'categories.id', '=', 'courses.categorie_id')
        ->leftJoin('courses_students', 'courses.id', '=', 'courses_students.course_id')
        ->groupBy('categories.name')  // Modificado para usar el nombre de la columna en GROUP BY
        ->orderBy('total_students')
        ->orderBy('categories.name')   // Agregado para ordenar por nombre de categoría si hay empate en total_students
        ->limit(1)
        ->get();

    return response()->json($result);
    }

    public function categoriaMasConsultada(){
        $result = Categorie::select('categories.name as category', DB::raw('count(courses_students.id) as total_students'))
            ->leftJoin('courses', 'categories.id', '=', 'courses.categorie_id')
            ->leftJoin('courses_students', 'courses.id', '=', 'courses_students.course_id')
            ->groupBy('categories.name')  // Usar el nombre de la columna en GROUP BY
            ->orderByDesc('total_students')
            ->orderByDesc('category')  // Ordenar por el nombre de la categoría si hay empate en total_students
            ->limit(1)
            ->get();

        return response()->json($result);
    }

    public function usersInMyCourse(){
        $userId = auth('api')->user()->id;

        $resultados = DB::table('courses_students')
            ->join('courses', 'courses_students.course_id', '=', 'courses.id')
            ->where('courses.user_id', $userId)
            ->select('courses_students.*')
            ->get();

        return $resultados;
    }

    public function tuActividad(){
        $userId = Auth::id();

        $classes = Course::where('user_id', $userId)
            ->join('course_sections', 'courses.id', '=', 'course_sections.course_id')
            ->join('course_classes', 'course_sections.id', '=', 'course_classes.course_section_id')
            ->select('course_classes.*')
            ->get();

        return $classes;
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
        $request->request->add(["user_id" => auth("api")->user()->id]);
        $sale = Sale::create($request->all());

        $carts = Cart::where("user_id",auth('api')->user()->id)->get();

        foreach ($carts as $key => $cart) {
            // $cart->delete();
            $new_detail = [];
            $new_detail = $cart->toArray();
            $new_detail["sale_id"] = $sale->id;
            SaleDetail::create($new_detail);
            CoursesStudent::create([
                "course_id" =>$new_detail["course_id"],
                "user_id" => auth('api')->user()->id,
            ]);
        }   

        // AQUI VA EL CODIGO PARA EL ENVIO DE CORREO
        Mail::to($sale->user->email)->send(new SaleMail($sale)); 
        return response()->json(["message" => 200, "message_text" => "LOS CURSOS SE HAN ADQUIRIDO CORRECTAMENTE"]);

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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
