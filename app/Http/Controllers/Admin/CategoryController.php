<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function store(Request $request) {
        $request->validate(['name' => 'required|unique:categories']);
        Category::create($request->all());
        return back()->with('success', 'Category added!');
    }

    public function update(Request $request, Category $category) {
        $request->validate(['name' => 'required']);
        $category->update($request->all());
        return back()->with('success', 'Category updated!');
    }

    public function destroy(Category $category) {
        $category->delete();
        return back()->with('success', 'Category deleted!');
    }
}
