<?php

namespace App\Http\Controllers\API;

use App\Product;
use Illuminate\Http\Request;

class ProductController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::all();

        foreach ($products as $product) {
            $product->imageUrl = $product->image ? asset('storage/pics/' . $product->image) : null;
        }
        return $this->sendResponse($products->toArray(), 'Products retrieved successfully.');
    }

    public function create(Request $request)
    {
        $product = Product::create([
            'title' => $request->title,
            'description' => $request->description,
            'detail' => $request->detail,
            'price' => $request->price
        ]);

        if($request->hasFile('image')){
            $image = $request->image;
            $ext = $image->getClientOriginalExtension();
            $filename = uniqid().'.'.$ext;
            $image->storeAs('public/pics',$filename);
            $product->image = $filename;
        }

        $product->save();

        return $this->sendResponse($product, 'Product created successfully');
    }
}
