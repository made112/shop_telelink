<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Category;
use App\SubSubCategory;
use App\Brand;
use App\Product;
use App\Language;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SubSubCategoryController extends Controller
{

    protected function validator(array $data, $type = 'add')
    {

        $validation_array = [
            "name" => "required|array|min:2",
            'category_id' => 'required|numeric',
            'sub_category_id' => 'required|numeric',
            'meta_title' => 'nullable|array|min:2',
            'meta_description' => 'nullable|array|min:2',
            "name.*" => "required|string|max:255",
            'meta_title.*' => 'nullable|string|max:255',
            'meta_description.*' => 'nullable|string|max:255',
        ];
        $validation_messages = [
            'name.required' => 'Name must be required.',
            'description.required' => 'Description must be required.',
            'meta_title.required' => 'Meta title must be required.',
            'meta_description.required' => 'Meta description must be required.',
            'category_id.required' => 'Category is required*.',
            'sub_category_id.required' => 'Subcategory must be required.',
        ];
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
        $subsubcategories = SubSubCategory::orderBy('created_at', 'desc');
        if ($request->has('search')) {
            if (preg_match('/^[^a-zA-Z0-9]+$/', $request->search)) // '/[^a-z\d]/i' should also work.
            {
                $request->search = str_replace("\"", "", json_encode(strtolower($request->search)));
                $pos = strpos($request->search,'\\');
                if ($pos !== false) {
                    $str = substr($request->search,0,$pos+1) . str_replace('\\','\\\\',substr($request->search,$pos+1));
                }
                $request->search = $str;
            }
            $sort_search = $request->search;
            $subsubcategories = $subsubcategories->where('name', 'like', '%' . $sort_search . '%');
        }
        $subsubcategories = $subsubcategories->paginate(15);
        return view('subsubcategories.index', compact('subsubcategories', 'sort_search'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::all();
        $brands = Brand::all();
        return view('subsubcategories.create', compact('categories', 'brands'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validator($request->all())->validate();

        // return $request;
        $subsubcategory = new SubSubCategory;
        $subsubcategory->name = $request->name;
        $subsubcategory->sub_category_id = $request->sub_category_id;
        //$subsubcategory->attributes = json_encode($request->choice_attributes);
        //$subsubcategory->brands = json_encode($request->brands);
        $subsubcategory->meta_title = $request->meta_title;
        $subsubcategory->meta_description = $request->meta_description;
        if ($request->slug != null) {
            $subsubcategory->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->slug));
        } else {
            $subsubcategory->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->name[config('translatable.DEFAULT_LANGUAGE', 'en')])) . '-' . Str::random(5);
        }

        $data = openJSONFile('en');
        $data[$subsubcategory->name] = $subsubcategory->name;
        saveJSONFile('en', $data);

        if ($subsubcategory->save()) {
            flash(__('SubSubCategory has been inserted successfully'))->success();
            return redirect()->route('subsubcategories.index');
        } else {
            flash(__('Something went wrong'))->error();
            return back();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $subsubcategory = SubSubCategory::findOrFail(decrypt($id));
        $categories = Category::all();
        $brands = Brand::all();
        return view('subsubcategories.edit', compact('subsubcategory', 'categories', 'brands'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validator($request->all(),'update')->validate();

        $subsubcategory = SubSubCategory::findOrFail($id);

        // foreach (Language::all() as $key => $language) {
        //     $data = openJSONFile($language->code);
        //     unset($data[$subsubcategory->name]);
        //     $data[$request->name] = "";
        //     saveJSONFile($language->code, $data);
        // }

        $subsubcategory->name = $request->name;
        $subsubcategory->sub_category_id = $request->sub_category_id;
        //$subsubcategory->attributes = json_encode($request->choice_attributes);
        //$subsubcategory->brands = json_encode($request->brands);
        $subsubcategory->meta_title = $request->meta_title;
        $subsubcategory->meta_description = $request->meta_description;
        if ($request->slug != null) {
            $subsubcategory->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->slug));
        } else {
            $subsubcategory->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->name[config('translatable.DEFAULT_LANGUAGE', 'en')])) . '-' . Str::random(5);
        }

        if ($subsubcategory->save()) {
            flash(__('SubSubCategory has been updated successfully'))->success();
            return redirect()->route('subsubcategories.index');
        } else {
            flash(__('Something went wrong'))->error();
            return back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $subsubcategory = SubSubCategory::findOrFail($id);
        Product::where('subsubcategory_id', $subsubcategory->id)->delete();
        if (SubSubCategory::destroy($id)) {
            foreach (Language::all() as $key => $language) {
                $data = openJSONFile($language->code);
                unset($data[$subsubcategory->name]);
                saveJSONFile($language->code, $data);
            }
            flash(__('SubSubCategory has been deleted successfully'))->success();
            return redirect()->route('subsubcategories.index');
        } else {
            flash(__('Something went wrong'))->error();
            return back();
        }
    }

    public function get_subsubcategories_by_subcategory(Request $request)
    {
        $subsubcategories = SubSubCategory::where('sub_category_id', $request->subcategory_id)->get();
        return $subsubcategories;
    }

    // public function get_brands_by_subsubcategory(Request $request)
    // {
    //     $brand_ids = json_decode(SubSubCategory::findOrFail($request->subsubcategory_id)->brands);
    //     $brands = array();
    //     foreach ($brand_ids as $key => $brand_id) {
    //         array_push($brands, Brand::findOrFail($brand_id));
    //     }
    //     return $brands;
    // }

    // public function get_attributes_by_subsubcategory(Request $request)
    // {
    //     $attribute_ids = json_decode(SubSubCategory::findOrFail($request->subsubcategory_id)->attributes);
    //     $attributes = array();
    //     foreach ($attribute_ids as $key => $attribute_id) {
    //         if(\App\Attribute::find($attribute_id) != null){
    //             array_push($attributes, \App\Attribute::findOrFail($attribute_id));
    //         }
    //     }
    //     return $attributes;
    // }
}
