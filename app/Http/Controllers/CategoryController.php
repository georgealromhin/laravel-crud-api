<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Category;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\CategoryResourceCollection;




class CategoryController extends Controller
{
    
    /*
    *   @return CategoryResource
    */
    public function show(Category $category): CategoryResource{
        return new CategoryResource($category);
    }
    /*
    *   @return CategoryResourceCollection
    */
    public function index(): CategoryResourceCollection{
        $category = Category::orderBy('id', 'desc')->get();
        return new CategoryResourceCollection($category);
    }
    /*
    *   @return CategoryResource
    */
    public function store(Request $request): CategoryResource{

        $request->validate(['name' => 'required']);
        //$category->name = $request->name;
        $category = Category::create($request->all());
        return(new CategoryResource($category));
    }
    /*
    *   @return CategoryResource
    */
    public function update(Category $category, Request $request): CategoryResource{
        //$request->validate(['name' => 'required']);
        $category->update($request->all());
        return(new CategoryResource($category));
    }
    /*
    *   @return 
    */
    public function destroy(Category $category){
        $category->delete();
		return response()->json();
    }
}
