<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Events\TripAccepted;
use App\Events\TripStarted;
use App\Events\TripEnded;
use App\Events\TripLocationUpdated;

class TripController extends Controller
{
    //

    public function store(Request $request) {
        $request->validate([
            'origin' => 'required', 
            'destination' => 'required', 
            'destination_name' => 'required',
        ]); 

        $trip = $request->user()->trips()->create($request->only([
            'origin', 
            'destination', 
            'destination_name',
        ]));

        TripCreated::dispatch($trip, $trip->user);

        return $trip;
    }

    public function show(Request $request, Trip $trip) {
        // nedd to make sure that trip is associated with authenticated user or driver
        if ($trip->user->id === $request->user()->id) {
            return $trip;
        }

        // the trip and the driver can have the "same" driver if both are null, so add check to ensure non null values
        if ($trip->driver && $request->driver) {
            if ($trip->driver->id === $request->driver()->id) {
                return $trip;
            }
        } 

        return response()->json(['message' => 'Cannot find this trip'], 404);
    }

    public function accept(Request $request, Trip $trip) {
        // a driver accepts a trip

        $request->validate([
            'driver_location' => 'required'
        ]);

        $trip->update([
            'driver_id' => $request->user()->id; 
            'driver_location' => $request->driver_location;
        ]); 

        //loading in driver and user relation model
        $trip->load('driver.user');

        TripAccepted::dispatch($trip, $request->user());

        return $trip;
    }

    public function start(Request $request, Trip $trip) {
        // a driver starts a trip 
        $trip->update([
            'is_started' => true
        ]);

        //loading in driver and user relation model
        $trip->load('driver.user');

        TripStarted::dispatch($trip, $request->user());

        return $trip; 
    }

    public function end(Request $request, Trip $trip) {
        // a driver ends a trip
        $trip->update([
            'is_complete' => true
        ]);

        //loading in driver and user relation model
        $trip->load('driver.user');

        TripEnded::dispatch($trip, $request->user());

        return $trip; 
    }

    public function location(Request $request, Trip $trip) {
        // updates the driver current location 
        $request->validate([
            'driver_location' => 'required'
        ]);

        $trip->update([
            'driver_location' => $request->driver_location
        ]); 

        $trip->load('driver.user'); 

        TripLocationUpdated::dispatch($trip, $trip->user);

        return $trip; 
    }
}
