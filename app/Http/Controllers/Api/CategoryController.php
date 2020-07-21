<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    private $rules = [ 'name' => 'required|max:255', 'is_active' => 'boolean'];

    public function index(Request $request)
    {
        $category = Category::all();

        return response()->json(['Categories' => $category], 200);
    }

    public function store(Request $request)
    {
        $this->validate($request, $this->rules);
        $category = Category::create($request->all());
        $category->refresh();
        return $category;
        //return response()->json([], 201);
    }

    public function show(Category $category)
    {
        return response()->json(['Categories' => $category], 200);
    }

    public function update(Request $request, Category $category)
    {
        $this->validate($request, $this->rules);
        $category->update($request->all());

        return $category;

        //return response()->json(['category' => $category], 200);
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return response()->noContent();
    }
}
