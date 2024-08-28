<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Category;
use App\HomeCategory;
use App\Product;
use App\Language;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    protected function validator(array $data, $type = 'add')
    {
        $validation_array = [
            "name"  => "required|array|min:2",
            'digital' => 'required|string|max:255',
            'meta_title' => 'nullable|array|min:2',
            'meta_description' => 'nullable|array|min:2',
            'banner' => 'required|mimes:jpg,png,jpeg,gif,svg',
            'icon' => 'required|mimes:jpg,png,jpeg,gif,svg',
            "name.*"  => "required|string|max:255",
            'meta_title.*' => 'nullable|string|max:255',
            'meta_description.*' => 'nullable|string|max:255',
        ];
        $validation_messages = [
            'name.required'=>'Name must be required.',
            'meta_title.required'=> 'Meta title is required*.',
            'meta_description.required'=>'Meta description is required*.',
            'meta_description.array'=>'Meta description must be array.',
            'banner.required' => 'Banner is required*.',
            'icon.required' => 'Icon is required*.',
            'digital.required' => 'Type is required*.',
            'digital.string' => 'Type must be a string',
            'banner.mimes' => 'Banner image extension must be [png, jpg, jpeg, jpeg, gif, svg]',
            'banner.max' => 'The file must be less than 2MB.',
            'icon.mimes' => 'Icon image extension must be [png, jpg, jpeg, jpeg, gif, svg]',
            'icon.max' => 'The file must be less than 2MB.',
        ];

        if($type == 'update'){
            $validation_array['banner'] = 'nullable|mimes:jpg,png,jpeg,gif,svg|max:2000';
            $validation_array['icon'] = 'nullable|mimes:jpg,png,jpeg,gif,svg|max:2000';
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
        $categories = Category::orderBy('created_at', 'desc');
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
            $categories = $categories->where('name', 'like', '%' . $sort_search . '%');
        }
        $categories = $categories->paginate(15);
        return view('categories.index', compact('categories', 'sort_search'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('categories.create');
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

        $category = new Category;
        $category->name = $request->name;
        $category->meta_title = $request->meta_title;
        $category->meta_description = $request->meta_description;
        if ($request->slug != null) {
            $category->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->slug));
        } else {
            $category->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->name[config('translatable.DEFAULT_LANGUAGE', 'en')])) . '-' . Str::random(5);
        }
        if ($request->commision_rate != null) {
            $category->commision_rate = $request->commision_rate;
        }

        $data = openJSONFile('en');
        $data[$category->name] = $category->name;
        saveJSONFile('en', $data);

        if ($request->hasFile('banner')) {
            $category->banner = $request->file('banner')->store('uploads/categories/banner');
        }
        if ($request->hasFile('icon')) {
            $category->icon = $request->file('icon')->store('uploads/categories/icon');
        }

        $category->digital = $request->digital;
        if ($category->save()) {
            flash(__('Category has been inserted successfully'))->success();
            return redirect()->route('categories.index');
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
        $category = Category::findOrFail(decrypt($id));
        return view('categories.edit', compact('category'));
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
        $this->validator($request->all(),'update')->validate();
        $category = Category::findOrFail($id);
        $category->name = $request->name;
        $category->meta_title = $request->meta_title;
        $category->meta_description = $request->meta_description;
        if ($request->slug != null) {
            $category->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->slug));
        } else {
            $category->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->name[config('translatable.DEFAULT_LANGUAGE', 'en')])) . '-' . Str::random(5);
        }

        if ($request->hasFile('banner')) {
            $category->banner = $request->file('banner')->store('uploads/categories/banner');
        }
        if ($request->hasFile('icon')) {
            $category->icon = $request->file('icon')->store('uploads/categories/icon');
        }
        if ($request->commision_rate != null) {
            $category->commision_rate = $request->commision_rate;
        }

        $category->digital = $request->digital;
        if ($category->save()) {
            flash(__('Category has been updated successfully'))->success();
            return redirect()->route('categories.index');
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
        if ($id == 15) {
            flash(__('category_not_deleted'))->error();
            return back();
        }
        $category = Category::findOrFail($id);
        foreach ($category->subcategories as $key => $subcategory) {
            foreach ($subcategory->subsubcategories as $key => $subsubcategory) {
                $subsubcategory->delete();
            }
            $subcategory->delete();
        }

        Product::where('category_id', $category->id)->delete();
        HomeCategory::where('category_id', $category->id)->delete();

        if (Category::destroy($id)) {
            foreach (Language::all() as $key => $language) {
                $data = openJSONFile($language->code);
                unset($data[$category->name]);
                saveJSONFile($language->code, $data);
            }

            if ($category->banner != null) {
                //($category->banner);
            }
            if ($category->icon != null) {
                //unlink($category->icon);
            }
            flash(__('Category has been deleted successfully'))->success();
            return redirect()->route('categories.index');
        } else {
            flash(__('Something went wrong'))->error();
            return back();
        }
    }

    public function updateFeatured(Request $request)
    {
        $category = Category::findOrFail($request->id);
        $category->featured = $request->status;
        if ($category->save()) {
            return 1;
        }
        return 0;
    }
}
