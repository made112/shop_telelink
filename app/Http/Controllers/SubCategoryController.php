<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SubCategory;
use App\SubSubCategory;
use App\Category;
use App\Product;
use App\Language;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SubCategoryController extends Controller
{
    protected function validator(array $data, $type = 'add')
    {
        $validation_array = [

            "name"  => "required|array|min:2",
            'category_id' => 'required|numeric',
            'meta_title' => 'nullable|array|min:2',
            'meta_description' => 'nullable|array|min:2',
            "name.*"  => "required|string|max:255",
            'meta_title.*' => 'nullable|string|max:255',
            'meta_description.*' => 'nullable|string|max:255',
        ];
        $validation_messages = [
            'name.required'=>'Name must be required.',
            'description.required'=>'Description must be required.',
            'meta_title.required'=>'Meta title must be required.',
            'meta_description.required'=>'Meta description must be required.',
            'meta_description.array'=>'Meta description must be array.',
            'category_id.required' => 'Category is required*.',
            ];

        return Validator::make($data, $validation_array, $validation_messages);

    }
    /**
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sort_search = null;
        $subcategories = SubCategory::orderBy('created_at', 'desc');
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
            $subcategories = $subcategories->where('name', 'like', '%' . $sort_search . '%');
        }
        $subcategories = $subcategories->paginate(15);
        return view('subcategories.index', compact('subcategories', 'sort_search'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::all();
        return view('subcategories.create', compact('categories'));
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
        $subcategory = new SubCategory;
        $subcategory->name = $request->name;
        $subcategory->category_id = $request->category_id;
        $subcategory->meta_title = $request->meta_title;
        $subcategory->meta_description = $request->meta_description;
        if ($request->slug != null) {
            $subcategory->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->slug));
        } else {
            $subcategory->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->name[config('translatable.DEFAULT_LANGUAGE', 'en')])) . '-' . Str::random(5);
        }

        $data = openJSONFile('en');
        $data[$subcategory->name] = $subcategory->name;
        saveJSONFile('en', $data);

        if ($subcategory->save()) {
            flash(__('Subcategory has been inserted successfully'))->success();
            return redirect()->route('subcategories.index');
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
        $subcategory = SubCategory::findOrFail(decrypt($id));
        $categories = Category::all();
        return view('subcategories.edit', compact('categories', 'subcategory'));
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
        $validations = $this->validator($request->all(), 'update');
        if($validations->fails()){
            $errors = '';
            foreach ($validations->errors()->all('') as $key=> $value) {
                $errors .= $value;
            }
            flash($errors)->error();
            return  back();
        }
        $this->validator($request->all(),'update')->validate();

        $subcategory = SubCategory::findOrFail($id);

        // foreach (Language::all() as $key => $language) {
        //     $data = openJSONFile($language->code);
        //     unset($data[$subcategory->name]);
        //     $data[$request->name] = "";
        //     saveJSONFile($language->code, $data);
        // }

        $subcategory->name = $request->name;
        $subcategory->category_id = $request->category_id;
        $subcategory->meta_title = $request->meta_title;
        $subcategory->meta_description = $request->meta_description;
        if ($request->slug != null) {
            $subcategory->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->slug));
        } else {
            $subcategory->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->name[config('translatable.DEFAULT_LANGUAGE', 'en')])) . '-' . Str::random(5);
        }

        if ($subcategory->save()) {
            flash(__('Subcategory has been updated successfully'))->success();
            return redirect()->route('subcategories.index');
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
        $subcategory = SubCategory::findOrFail($id);
        foreach ($subcategory->subsubcategories as $key => $subsubcategory) {
            $subsubcategory->delete();
        }
        Product::where('subcategory_id', $subcategory->id)->delete();
        if (SubCategory::destroy($id)) {
            foreach (Language::all() as $key => $language) {
                $data = openJSONFile($language->code);
                unset($data[$subcategory->name]);
                saveJSONFile($language->code, $data);
            }
            flash(__('Subcategory has been deleted successfully'))->success();
            return redirect()->route('subcategories.index');
        } else {
            flash(__('Something went wrong'))->error();
            return back();
        }
    }


    public function get_subcategories_by_category(Request $request)
    {
        $subcategories = SubCategory::where('category_id', $request->category_id)->get();
        return $subcategories;
    }
}
