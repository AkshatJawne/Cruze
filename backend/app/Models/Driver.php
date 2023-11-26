<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory;

    protected $guarded = []; // Allow mass assignments

    public function user() {
        $this->belongsTo(User::class); // A driver belongs to a user
    }

    public function trips() {
        $this->hasMany(Trip::class) // A driver has many trips
    }
}
