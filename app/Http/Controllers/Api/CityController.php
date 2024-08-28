<?php

namespace App\Http\Controllers\Api;

use App\City;
use App\Http\Controllers\Controller;
use App\Http\Resources\CityCollection;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function index() {
        return new CityCollection(City::where('status', 1)->get());
    }
}
