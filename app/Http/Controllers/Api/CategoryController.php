<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Model\Category;
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
        Category::create($request->all());

        return response()->json([], 200);
    }

    public function show(Category $category)
    {
        return response()->json(['Categories' => $category], 200);
    }

    public function update(Request $request, Category $category)
    {
        $this->validate($request, $this->rules);
        $category->update($request->all());
        return response()->json(['category' => $category], 200);
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return response()->noContent();
    }
}
