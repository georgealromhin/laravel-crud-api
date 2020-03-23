<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductResourceCollection;
use App\Product;

class ProductController extends Controller
{
    //http://localhost/crud-api/public/images/image-placeholder.png

    
    /*
    *   @return ProductResource
    */
    public function show(Product $product): ProductResource{
        return new ProductResource($product);
    }
    /*
    *   @return ProductResourceCollection
    */
    public function index(): ProductResourceCollection{
        $product = Product::with('category')->orderBy('id', 'desc')->get();
        return new ProductResourceCollection($product);
    }
    /*
    *   @return ProductResource
    */
    public function store(Product $product, Request $request): ProductResource{

        $request->validate([
            'name' => 'required',
            'price' => 'required',
            'category_id' => 'required'
        ]);
        
        //
        if($request->hasFile('image'))
        {
            $image_name = time().'_'.rand(999,9999).'.'.$request->image->getClientOriginalExtension();
            $request->image->move(public_path('images'), $image_name);
            $product->image = 'http://localhost/crud-api/public/images/'.$image_name;

        }else{     

            $product->image = 'http://localhost/crud-api/public/images/image-placeholder.png';
            
        }
        $product->name = $request->name;
        $product->price = $request->price;
        $product->category_id = $request->category_id;
        $product->save();

        return(new ProductResource($product));
    }
    /*
    *   @return ProductResource
        https://url/id?_method=put
    */
    public function update(Product $product, Request $request): ProductResource{

        if($request->hasFile('image'))
        {
            $image_name = time().'_'.rand(999,9999).'.'.$request->image->getClientOriginalExtension();
            $request->image->move(public_path('images'), $image_name);
            $product->image = 'http://localhost/crud-api/public/images/'.$image_name;
        }
        $product->name = $request->name;
        $product->price = $request->price;
        $product->category_id = $request->category_id;
        $product->save();

        return(new ProductResource($product));

    }
    /*
    *   @return 
    */
    public function destroy(Product $product){
        $product->delete();
		return response()->json();
    }
}
