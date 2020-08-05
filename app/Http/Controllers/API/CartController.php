<?php

namespace App\Http\Controllers\API;

use App\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends BaseController
{
    public function store(Request $request)
    {
        $user = Auth::user() ?? Auth::guard("api")->user();
        $url = 'https://s3.' . env('AWS_DEFAULT_REGION') . '.amazonaws.com/' . env('AWS_BUCKET') . '/images/';
        $input = $request->all();

        $cart = Cart::create();

        $cart->products()->sync([$input['productId'] => ['count' => $input['count']]]);

        // Check if auth
        if ($user) {
            $cart->user_id = $user->id;
            $cart->save();
        }

        $cart = Cart::with('products')->find($cart->id);

        foreach ($cart->products as $product) {
            $product->imageUrl = $product->image ? $url . $product->image : null;
        }

        return $this->sendResponse($cart, 'Cart created successfully');
    }

    public function show($id)
    {
        $cart = Cart::with('products')->find($id);
        $url = 'https://s3.' . env('AWS_DEFAULT_REGION') . '.amazonaws.com/' . env('AWS_BUCKET') . '/images/';
        if (is_null($cart)) {
            return $this->sendError('Cart not found.');
        }

        foreach ($cart->products as $product) {
            $product->imageUrl = $product->image ? $url . $product->image : null;
        }

        return $this->sendResponse($cart, 'Cart retrieved successfully.');
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user() ?? Auth::guard("api")->user();
        $url = 'https://s3.' . env('AWS_DEFAULT_REGION') . '.amazonaws.com/' . env('AWS_BUCKET') . '/images/';
        $inputs = $request->all();
        $cart = Cart::find($id);
        $products = [];

        foreach ($inputs['items'] as $input) {
            $products[$input['id']] = ['count' => $input['pivot']['count']];
        }
        $cart->products()->sync($products);

        if ($user && !$cart->user_id) {
            $cart->user_id = $user->id;
            $cart->save();
        }

        $cart = Cart::with('products')->find($id);

        foreach ($cart->products as $product) {
            $product->imageUrl = $product->image ? $url . $product->image : null;
        }

        return $this->sendResponse($cart, 'Cart updated successfully');
    }
}
