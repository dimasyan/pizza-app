<?php

namespace App\Http\Controllers\API;


use App\Cart;
use Illuminate\Http\Request;
use App\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OrderController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $orders = Order::where('user_id', $user->id)->with('products')->get();
        return $this->sendResponse($orders->toArray(), 'Orders retrieved successfully.');
    }

    public function create(Request $request)
    {
        $user = Auth::user() ?? Auth::guard("api")->user();
        $input = $request->all();

        $validator = Validator::make($input, [
            'address' => 'required',
            'city' => 'required',
            'country' => 'required',
            'postcode' => 'required',
            'cart_id' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        if ($user) {
            $input['email'] = $user->email;
            $input['name'] = $user->name;
            $input['phone'] = $user->phone;
        }

        $cart = Cart::find($input['cart_id']);
        $products = [];

        $cost = 0;

        foreach ($cart->products as $product) {
            $products[$product->id] = [
                'price' => $product->price,
                'count' => $product->pivot->count,
                'total' => $product->price * $product->pivot->count
            ];
            $cost += $product->price * $product->pivot->count;
        }
        $input['products_cost'] = $cost;
        $input['delivery_price'] = Order::DELIVERY_PRICE;
        $input['total_cost'] = $cost + Order::DELIVERY_PRICE;

        $order = Order::create($input);
        $order->products()->sync($products);
        if ($user) {
            $order->user_id = $user->id;
        }
        $order->save();

        return $this->sendResponse($order, 'Order created successfully');
    }
}
