<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use Exception;
use Illuminate\Support\Facades\Auth;

class CustomerAuthController extends Controller
{
    public function register(Request $request){
    
        $validateData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|max:255|unique:customers',
            'password' => 'required|string|min:8'
        ]);

        try {            
            $customer = Customer::create([
                'first_name' => $validateData['first_name'],
                'last_name' => $validateData['last_name'],
                'email' => $validateData['email'],
                'password' => $validateData['password']
            ]);
            
            $token = $customer->createToken('auth_customer_token', ['customer'])->plainTextToken;

            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'msg' => config('app.env', 'local')
            ], 500);
        }
    }

    public function login(Request $request){

        if(!Auth::attempt($request->only('email', 'password'))){
            return response()->json([
                'message' => 'Invalid login details'
            ], 401);
        }

        $customer = Customer::where('email', $request['email'])->firstOrFail();

        $token = $customer->createToken('auth_customer_token', ['customer'])->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer'
        ]);
    }

    public function userinfo(Request $request){
        return $request->user();
    }
}
