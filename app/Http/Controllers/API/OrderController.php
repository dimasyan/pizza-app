<?php

namespace App\Http\Controllers\API;


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
            'postcode' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $order = Order::create($input);

        if (!$user) {
            $user = User::create([
                'name' => $input['name'],
                'email' => $input['email']
            ]);
        }
        $order->user_id = $user->id;
        $order->save();

        return $this->sendResponse($order, 'Order created successfully');
    }
}
