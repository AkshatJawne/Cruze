<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DriverController extends Controller
{
    public function show(Request $request) {
        // need to return back the user and the corresponding driver model 
        $user = $request->user();
        // determines if there is a driver relationship on the given user, and will inject one through load model if it exists
        $user->load('driver');

        return $user;
    }

    public function update(Request $request) {
        // update driver information based on request
        $request->validate([
            'year' => 'required|numeric|between:2000,2024', 
            'make' => 'required', 
            'model' => 'required', 
            'color' => 'required|alpha', 
            'licence_plate' => 'required', 
            'name' => 'required', 
        ]); 

        $user = $request->user();
        // only property from user that is impacted through update is user's name
        $user->update($request->only('name'));

        // create or update a driver associated with this user
        $user->driver()->updateOrCreate($request->only([
             'year', 
             'make',
             'model'
             'color', 
             'licence_plate ' 
        ]));

        $user->load('driver'); 

        return $user;
    }
}
