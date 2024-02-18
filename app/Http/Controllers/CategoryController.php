<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class CategoryController extends Controller
{
    function index() {
        $categories = Category::paginate(10);
        return view('pages.categories.index', compact('categories'));
    }

    public function create() {
        return view('pages.categories.create');
    }

    public function store(Request $request) {
        //validate the request
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        //store the request
        $category = new Category;
        $category->name = $request->name;
        $category->description = $request->description;
        $category->save();

        //save image
        if($request->hasFile('image')){
            $image = $request->file('image');
            $image->storeAs('public/categories', $category->id . '.' . $image->getClientOriginalExtension());
            $category->image = 'storage/categories/' . $category->id . '.' . $image->getClientOriginalExtension();
            $category->save();
        }

        return redirect()->route('categories.index')->with('success', 'Category created successfully');
    }

    public function edit ($categoryId)
    {
        $category = Category::findOrFail($categoryId);
        return view('pages.categories.edit', compact('category'));
    }

    public function update(Request $request, $categoryId)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'image' => 'nullable|image|mimes:png,jpg,jpeg,gif,svg|max:2048'
        ]);

        //update the request
        $category = Category::find($categoryId);
        $category->name = $request->name;
        $category->description = $request->description;
        $category->save();
        //save image
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $image->storeAs('public/categories', $category->id . '.' . $image->getClientOriginalExtension());
            $category->image = 'storage/categories/' . $category->id . '.' . $image->getClientOriginalExtension();
            $category->save();
        }

        return redirect()->route('categories.index')->with('success', 'Category updated successfully');
    }

    //destroy
    public function destroy($categoryId) {
        $category = Category::find($categoryId);
        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Category deleted successfully');
    }
}
