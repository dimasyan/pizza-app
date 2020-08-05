<?php

namespace App\Http\Controllers\API;

use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
        $url = 'https://s3.' . env('AWS_DEFAULT_REGION') . '.amazonaws.com/' . env('AWS_BUCKET') . '/images/';

        foreach ($products as $product) {
            $product->imageUrl = $product->image ? $url . $product->image : null;
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
            Storage::disk('s3')->put('images/' . $filename, file_get_contents($image));
            //$image->storeAs('public/pics',$filename);
            $product->image = $filename;
        }

        $product->save();

        return $this->sendResponse($product, 'Product created successfully');
    }
}
