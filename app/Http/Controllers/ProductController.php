<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\VarDumper\Caster\RedisCaster;

class ProductController extends Controller
{
    function index()
    {
        $products = Product::paginate(10);
        return view('pages.products.index', compact('products'));
    }

    function create() {
        $categories = DB::table('categories')->get();
        return view('pages.products.create',compact('categories'));
    }

    public function store(Request $request) {
        //validate
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'price' => 'required|numeric',
            'category_id' => 'required',
            'stock' => 'required|numeric',
            'status' => 'required|boolean',
            'is_favorite' => 'required|boolean',
        ]);

        //store the request
        $product = new Product;
        $product->name = $request->name;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->category_id = $request->category_id;
        $product->stock = $request->stock;
        $product->status = $request->status;
        $product->is_favorite = $request->is_favorite;
        $product->save();

        //save image
        if($request->hasFile('image')){
            $image = $request->file('image');
            $image->storeAs('public/products', $product->id . '.' . $image->getClientOriginalExtension());
            $product->image = 'storage/products/' . $product->id . '.' . $image->getClientOriginalExtension();
            $product->save();
        }

        return redirect()->route('products.index')->with('success', 'Product created successfully');

    }

    function show($productId) {
        return view('pages.products.show');
    }

    function edit($productId) {

        $categories = DB::table('categories')->get();
        $product = Product::findOrFail($productId);
        return view('pages.products.edit', compact('product', 'categories'));
    }

    function update(Request $request, $productId) {
        //validate the request
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'price' => 'required|numeric',
            'category_id' => 'required',
            'stock' => 'required|numeric',
            'status' => 'required|boolean',
            'is_favorite' => 'required|boolean',
        ]);

        // //update the request
        $product = Product::find($productId);
        $product->name = $request->name;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->category_id = $request->category_id;
        $product->stock = $request->stock;
        $product->status = $request->status;
        $product->is_favorite = $request->is_favorite;
        $product->save();

          //save image
          if($request->hasFile('image')){
            $image = $request->file('image');
            $image->storeAs('public/products', $product->id . '.' . $image->getClientOriginalExtension());
            $product->image = 'storage/products/' . $product->id . '.' . $image->getClientOriginalExtension();
            $product->save();
        }

        return redirect()->route('products.index')->with('success', 'Product updated successfully');

    }

    function destroy($productId) {
        // delete the request
        $product = Product::find($productId);
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully');
    }
}
