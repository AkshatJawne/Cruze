<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function submit(Request $request) {
        // Validate the request
        $request->validate([
            'phone' => 'required|numeric|min:10',
        ])
        
        // Find or create a user
        $user = User::firstOrCreate([
            'phone' => $request->phone 
        ]); // Find or create a user

        if (!$user) {
            return response()->json([
                'message' => 'Could not process a user with that phone number'
            ], 401);
        }

        // Send the user a one-time verification code
        $user->notify(new LoginNeedsVerification()); 

        // Return a success message
        return response()->json([
            'message' => 'Verification code sent'
        ]); 
    };

    public function verify(Request $request) {
        // Validate the incoming request
        $request->validate([
            'phone' => 'required|numeric|min:10', 
            'login_code' => 'required|numeric|between:111111,999999'
        ]); 
        
        // Find the user
        $user = User::where('phone', $request->phone)->where('login_code', $request->login_code)->first();

        // If code provided is correct, send back auth token
        if ($user) {

            $user->update([
                'login_code' => null
            ]); // Clear the login code

            return $user->createToken($request->login_code)->plainTextToken; // Create a new token
        }

        return response()->json([
            'message' => 'Invalid verification code'
        ], 401);


    }

}
