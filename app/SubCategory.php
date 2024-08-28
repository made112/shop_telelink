<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class SubCategory extends Model
{
    use HasTranslations;

    public $translatable = ['name', 'meta_title', 'meta_description'];
    protected $fillable = [
        'name', 'category_id', 'subcategory_id', 'meta_title', 'meta_description'
    ];
    public function getNameAttribute($value)
    {

        if ($value == '' || json_decode($value, true)) {
            $Original = $this->getOriginal('name');
            $tmp = json_decode($Original, true);
            $DEFAULT_LANGUAGE = config('translatable.DEFAULT_LANGUAGE', 'en');
            if ($tmp && isset($tmp[$DEFAULT_LANGUAGE])) {
                return  $tmp[$DEFAULT_LANGUAGE];
            }
            return $Original;
        }

        return $value;
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subsubcategories()
    {
        return $this->hasMany(SubSubCategory::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'subcategory_id');
    }

    public function classified_products()
    {
        return $this->hasMany(CustomerProduct::class, 'subcategory_id');
    }
}
