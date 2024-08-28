<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;


/**
 * App\Models\SubCategory
 *
 * @property int $id
 * @property string $name
 * @property int $category_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubCategory whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SubCategory whereUpdatedAt($value)
 * @mixin \Eloquent
 */

class SubCategory extends Model
{
    use HasTranslations;

    public $translatable = ['name', 'meta_title', 'meta_description'];

    public function getNameAttribute($value)
    {

        if ($value == '') {
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

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function subSubCategories()
    {
        return $this->hasMany(SubSubCategory::class);
    }
}
