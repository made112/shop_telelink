<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Brand;
use App\Product;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BrandController extends Controller
{

    protected function validator(array $data, $type  = 'add')
    {
        $validation_array = [
            "name" => "required|array|min:2",
            'meta_title' => 'required|array|min:2',
            'meta_description' => 'nullable|array|min:2',
            'logo' => 'required|mimes:jpg,png,jpeg,gif,svg|max:2000',
            "name.*"  => "required|string|max:255",
            'meta_title.*' => 'required|string|max:255',
            'meta_description.*' => 'nullable|string|max:500',
        ];
        $validation_messages = [
            'name.required'=>'Name must be required*.',
            'meta_title.required'=>'Meta title must be required.',
            'meta_description.required'=>'Meta description must be required.',
            'meta_description.array'=>'Meta description must be array.',
            'logo.required' => 'Logo must be required*.',
            'logo.mimes' => 'Logo image extension must be [png, jpg, jpeg, jpeg, gif, svg]',
            'logo.max' => 'The file must be less than 2MB.'
        ];
        if($type === 'update') {
            $validation_array['logo'] = 'nullable|mimes:jpg,png,jpeg,gif,svg|max:2000';
        }
        return Validator::make($data, $validation_array, $validation_messages);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sort_search = null;
        $brands = Brand::orderBy('created_at', 'desc');
        if ($request->has('search')) {
            $sort_search = $request->search;
            $brands = $brands->where('name', 'like', '%' . $sort_search . '%');
        }
        $brands = $brands->paginate(15);
        return view('brands.index', compact('brands', 'sort_search'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('brands.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validator($request->all())->validate();
        $brand = new Brand;
        $brand->name = $request->name;
        $brand->meta_title = $request->meta_title;
        $brand->meta_description = $request->meta_description;
        if ($request->slug != null) {
            $brand->slug = str_replace(' ', '-', $request->slug);
        } else {
            $brand->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->name[config('translatable.DEFAULT_LANGUAGE', 'en')])) . '-' . Str::random(5);
        }
        if ($request->hasFile('logo')) {
            $brand->logo = $request->file('logo')->store('uploads/brands');
        }

        if ($brand->save()) {
            flash(__('Brand has been inserted successfully'))->success();
            return redirect()->route('brands.index');
        } else {
            flash(__('Something went wrong'))->error();
            return back();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $brand = Brand::findOrFail(decrypt($id));
        return view('brands.edit', compact('brand'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validator($request->all(), 'update')->validate();
        $brand = Brand::findOrFail($id);
        $brand->name = $request->name;
        $brand->meta_title = $request->meta_title;
        $brand->meta_description = $request->meta_description;
        if ($request->slug != null) {
            $brand->slug = str_replace(' ', '-', $request->slug);
        } else {
            $brand->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->name[config('translatable.DEFAULT_LANGUAGE', 'en')])) . '-' . Str::random(5);
        }
        if ($request->hasFile('logo')) {
            $brand->logo = $request->file('logo')->store('uploads/brands');
        }

        if ($brand->save()) {
            flash(__('Brand has been updated successfully'))->success();
            return redirect()->route('brands.index');
        } else {
            flash(__('Something went wrong'))->error();
            return back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $brand = Brand::findOrFail($id);
        Product::where('brand_id', $brand->id)->delete();
        if (Brand::destroy($id)) {
            if ($brand->logo != null) {
                //unlink($brand->logo);
            }
            flash(__('Brand has been deleted successfully'))->success();
            return redirect()->route('brands.index');
        } else {
            flash(__('Something went wrong'))->error();
            return back();
        }
    }
}
