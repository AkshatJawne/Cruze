<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    use HasFactory;

    protected $guarded = []; // Allow mass assignments

    // have to add casts based on migration exepecting these variables to be of a different format when being added to MySQL
    protected $casts = [
        'origins' => 'array',
        'destination' => 'array', 
        'driver_location' => 'array', 
        'is_started' => 'boolean', 
        'is_completed' => 'boolean', 
    ]

    public function user() {
        $this->belongsTo(User::class); // A trip belongs to a user
    }

    public function driver() {
        $this->belongsTo(Driver::class); // A trip belongs to a driver
    }
}
