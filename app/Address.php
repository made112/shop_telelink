<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [ 'address', 'country', 'city', 'postal_code', 'phone'
    ];
}
