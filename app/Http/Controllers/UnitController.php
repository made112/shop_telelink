<?php

namespace App\Http\Controllers;

use App\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index() {
        $units = Unit::all();
        return view('business_settings.unit', compact('units'));
    }

    public function updateYourUnit(Request $request)
    {
        $unit = Unit::findOrFail($request->id);
        $unit->name = $request->name;
        $unit->symbol = $request->symbol;
        $unit->code = $request->code;
        $unit->status = $unit->status;
        if($unit->save()){
            flash('Unit updated successfully')->success();
            return redirect()->route('unit.index');
        }
        else {
            flash('Something went wrong')->error();
            return redirect()->route('unit.index');
        }
    }

    public function create()
    {
        return view('partials.unit_create');
    }

    public function edit(Request $request)
    {
        $unit = Unit::findOrFail($request->id);
        return view('partials.unit_edit', compact('unit'));
    }

    public function store(Request $request)
    {
        $unit = new Unit;
        $unit->name = $request->name;
        $unit->symbol = $request->symbol;
        $unit->code = $request->code;
        $unit->status = 1;
        if($unit->save()){
            flash('Unit updated successfully')->success();
            return redirect()->route('unit.index');
        }
        else {
            flash('Something went wrong')->error();
            return redirect()->route('unit.index');
        }
    }

    public function update_status(Request $request)
    {
        $unit = Unit::findOrFail($request->id);
        $unit->status = intval($request->status);
        if($unit->save()){
            return 1;
        }
        return 0;
    }
}
