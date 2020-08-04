<?php

namespace App\Http\Controllers\API;

use App\Order;
use App\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class RegisterController extends BaseController
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'phone' => 'required',
            'c_password' => 'required|same:password',
        ]);
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        if (array_key_exists('orderId', $input) && $input['orderId']) {
            $order = Order::find($input['orderId']);
            $order->user_id = $user->id;
            $order->save();
        }
        $success['token'] =  $user->createToken('inno-pizza')->accessToken;
        $success['name'] =  $user->name;
        return $this->sendResponse($success, 'User register successfully.');
    }
}
