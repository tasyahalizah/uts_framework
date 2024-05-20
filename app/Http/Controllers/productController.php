<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Categories;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    //Menambahkan data ke database
    public function store(Request $request)
    {
        // Memvalidasi inputan
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'image' => 'required|image',
            'category_id' => 'required|string',
            'expired_at' => 'required|date',
        ]);

        // Kondisi apabila inputan yang digunakan tidak sesuai
        if ($validator->fails()) {
            return response()->json($validator->messages())->setStatusCode(422);
        }

        // Cari ID kategori berdasarkan nama
        $category = Categories::where('name', $request->input('category_id'))->first();

        if (!$category) {
            return response()->json(['error' => 'Kategori tidak ditemukan'], 404);
        }

        // Ambil pengguna yang diautentikasi dari request
        $user = $request->auth;

        $validated = $validator->validated();

        // Buat entri produk baru
        $product = Product::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'category_id' => $category->id,
            'expired_at' => $validated['expired_at'],
            'modified_by' =>$user->email
        ]);

        if ($request->hasFile('image')) {
            // Simpan file gambar dan dapatkan path-nya
            $path = $request->file('image')->store('images', 'public');
            $product->image = $path;
            $product->save();
        }

        // response json akan dikirim jika inputan benar
        return response()->json([
            'msg' => 'Data produk berhasil disimpan'
        ], 201);
    }

    //Show data
    function showAll(){
        $product = Product::all();
         return response()-> json([
            'msg' => 'Data Produk Keseluruhan',
            'data' => $product
         ],200);
    }

    //Delete Data
    public function delete($id){
        $product = Product::where('id', $id)->get();

        if($product){
            Product::where('id', $id)->delete();

            return response()->json([
                'msg' => 'Data produk dengan ID: '.$id.' berhasil dihapus'],201);
        }
        return response()->json([
            'msg' => 'Data produk dengan ID:'.$id.'tidak ditemukan'
        ],404);
    }

    //Show By ID
    public function showById($id){
        $product = Product::find($id);

        if ($product){
            return response()->json([
                "msg"=>'Data produk dengan ID: '.$id,
                'data'=> $product
            ], 200);
        }
        return response()->json([
            'msg' =>'Data produk dengan ID: '.$id.' tidak ditemukan',
        ], 404);
    }

    //Show By Name
    public function showByName($name){
        $product = Product::find($name);

        //cari data berdasarkan nama produk yang mirip
        $product = Product::where('name','LIKE','%'.$name.'%')->get();

        //apabila data produk ada
        if($product->count() > 0){
            return response()->json([
                'msg' => "Data Produk dengan nama yang mirip: ".$name,
                'data' => $product
            ],200);
        }

        //response ketika data tidak ada
        return response()->json([
            'msg' => 'Data produk dengan nama yang mirip: '.$name.' tidak ditemukan',
        ],404);
    }



    public function update(Request $request, $id) {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'category_id'=> 'sometimes|string', 
            'description'=> 'sometimes|string',
            'price' => 'sometimes|numeric',
            'image'=> 'sometimes|image', 
            'expired_at' => 'sometimes|date'
        ]);
    
        if($validator->fails()) {
            return response()->json($validator->getMessageBag())->setStatusCode(422);
        }
    
        $validated = $validator->validated();
        $product = Product::find($id);
    
        if (!$product) {
            return response()->json("Data dengan id : {$id} tidak ditemukan", 404);
        }
    
        if ($request->has('category_id')) {
            $category = Categories::where('name', $request->input('category_id'))->first();
            if (!$category) {
                return response()->json(['error' => 'Kategori tidak ditemukan'], 404);
            }
            $validated['category_id'] = $category->id;
        }
    
        $user = $request->auth;
        $validated['modified_by'] = $user->email;
    
        
        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $path = $request->file('image')->store('images', 'public');
            $validated['image'] = $path;
        }
    
        $product->update($validated);
    
        return response()->json("Data dengan id : {$id} berhasil di update", 200);
    }



}