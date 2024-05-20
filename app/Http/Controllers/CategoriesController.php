<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Categories;

class CategoriesController extends Controller
{
    //Menambahkan data ke database
    public function store(Request $request){

        //Memvalidasi inputan
        $validator = Validator::make($request ->all(),[
            'name' => 'required|max:255'
        ]);


        // Kondisi apabila inputan yang digunakan tidak sesuai
        if($validator->fails()){
            return response()->json($validator->messages())->setStatusCode(422);
        }


        $validated = $validator->validated();
        //masukkan inputan yang benar ke dalam database (table categories)
        Categories::create([
            'name' => $validated['name']
        ]);


        //response json akan dikirim jika inputan benar
        return response()->json([
            'msg' => 'Data kategori berhasil disimpan'
        ],201);
    }



    //Show data
    function showAll(){
        $categories = Categories::all();
         return response()-> json([
            'msg' => 'Data kategori Keseluruhan',
            'data' => $categories
         ],200);
    }


    
    //Update Data
    public function update( Request $request, $id){
        $validator = validator::make($request ->all(),[
            'name' => 'required|string|max:255'
        ]);


        // Kondisi apabila inputan yang digunakan tidak sesuai
        if($validator->fails()){
            return response()->json($validator->messages())->setStatusCode(422);
        }

        $validated =$validator->validated();
        Categories::where('id', $id)->update([
            'name' => $validated['name']
        ]);

        return response()->json([
            'msg' => 'Data kategori berhasil diubah'
        ],201);
    }



    //Delete Data
    public function delete($id){
        $categories = Categories::where('id', $id)->get();

        if($categories){
            Categories::where('id', $id)->delete();

            return response()->json([
                'msg' => 'Data kategori dengan ID: '.$id.' berhasil dihapus'],201);
        }
        return response()->json([
            'msg' => 'Data kategori dengan ID:'.$id.'tidak ditemukan'
        ],404);
    }



    //Show By ID
    public function showById($id){
        $categories = Categories::find($id);

        if ($categories){
            return response()->json([
                "msg"=>'Data kategori dengan ID: '.$id,
                'data'=> $categories
            ], 200);
        }
        return response()->json([
            'msg' =>'Data kategori dengan ID: '.$id.' tidak ditemukan',
        ], 404);
    }



    //Show By Name
    public function showByName($name){
        $categories = Categories::find($name);

        //cari data berdasarkan nama kategori yang mirip
        $categories = Categories::where('name','LIKE','%'.$name.'%')->get();

        //apabila data kategori ada
        if($categories->count() > 0){
            return response()->json([
                'msg' => "Data kategori dengan nama yang mirip: ".$name,
                'data' => $categories
            ],200);
        }

        //response ketika data tidak ada
        return response()->json([
            'msg' => 'Data kategori dengan nama yang mirip: '.$name.' tidak ditemukan',
        ],404);
    }
}