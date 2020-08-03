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
        $input = $request->all();

        $cart = Cart::create();

        $cart->products()->sync([$input['productId'] => ['count' => $input['count']]]);

        // Check if auth
        if ($user) {
            $cart->user_id = $user->id;
            $cart->save();
        }

        return $this->sendResponse(Cart::with('products')->find($cart->id), 'Cart created successfully');
    }

    public function show($id)
    {
        $cart = Cart::with('products')->find($id);
        if (is_null($cart)) {
            return $this->sendError('Cart not found.');
        }
        return $this->sendResponse($cart, 'Cart retrieved successfully.');
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user() ?? Auth::guard("api")->user();
        $inputs = $request->all();
        $cart = Cart::find($id);
        $products = [];

        foreach ($inputs['items'] as $input) {
            $products[$input['id']] = ['count' => $input['pivot']['count']];
        }
        $cart->products()->sync($products, false);

        if ($user && !$cart->user_id) {
            $cart->user_id = $user->id;
            $cart->save();
        }

        return $this->sendResponse(Cart::with('products')->find($cart->id), 'Cart updated successfully');
    }
}
