<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Product extends Model
{
  use HasTranslations;

  public $translatable = ['name', 'description', 'tags', 'meta_title', 'meta_description'];
  protected $fillable = [
    'name', 'added_by', 'refundable', 'user_id', 'category_id', 'subcategory_id', 'subsubcategory_id', 'brand_id', 'video_provider', 'video_link', 'unit_price',
    'purchase_price', 'unit', 'slug', 'colors', 'choice_options', 'variations', 'current_stock'
  ];

  public function getNameAttribute($value)
  {

      if ($value == '' ) {
          $Original = $this->getOriginal('name');
          $tmp= json_decode($Original,true);
          $DEFAULT_LANGUAGE=config('translatable.DEFAULT_LANGUAGE','en');
          if($tmp && isset($tmp[$DEFAULT_LANGUAGE])){
              return  $tmp[$DEFAULT_LANGUAGE];
          }
          return $Original;
    }

        return $value;

  }

  public function getThumbnailImgAttribute($value) {
      if(is_numeric($value)){
          $tmp = $this->getOriginal('thumbnail_img');
          return uploaded_asset($tmp);
      }
        return $value;
  }
    public function getPhotosAttribute($value) {
      if (is_array(json_decode($value))){
          return $value;
      }
        if(is_string($value)){
            $tmp = $this->getOriginal('photos');
            $tmp_arr = array();
            $origin_arr = explode(',', $tmp);
            foreach ($origin_arr as $item) {
                array_push($tmp_arr, uploaded_asset($item));
            }
            return json_encode($tmp_arr);
        }
        return $value;
    }

    public function getMetaImgAttribute($value) {
        if(is_numeric($value)){
            $tmp = $this->getOriginal('thumbnail_img');
            return uploaded_asset($tmp);
        }
        return $value;
    }

    public function getUnitAttribute($value)
    {
        if (is_object(json_decode($value))) {
            $val = (array) json_decode($value);
            if ($val[app()->getLocale()] !== null and $val[app()->getLocale()] !== '') {
                return $val[app()->getLocale()];
            } else {
                return $val[config('app.locale')];
            }
        }
        if (is_string($value)){
            return $value;
        }else {
            if ($value == '' ) {
                $Original = $this->getOriginal('unit');
                $tmp= json_decode($Original,true);
                $DEFAULT_LANGUAGE=config('translatable.DEFAULT_LANGUAGE','en');
                if($tmp && isset($tmp[$DEFAULT_LANGUAGE])){
                    return  $tmp[$DEFAULT_LANGUAGE];
                }
                return $Original;
            }
        }

        return $value;

    }

    public function getDescriptionAttribute($value)
    {

        if ($value == '' ) {
            $Original = $this->getOriginal('description');
            $tmp= json_decode($Original,true);
            $DEFAULT_LANGUAGE=config('translatable.DEFAULT_LANGUAGE','en');
            if($tmp && isset($tmp[$DEFAULT_LANGUAGE])){
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

  public function subcategory()
  {
    return $this->belongsTo(SubCategory::class);
  }

  public function subsubcategory()
  {
    return $this->belongsTo(SubSubCategory::class);
  }

  public function brand()
  {
    return $this->belongsTo(Brand::class);
  }

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function orderDetails()
  {
    return $this->hasMany(OrderDetail::class);
  }

  public function reviews()
  {
    return $this->hasMany(Review::class)->where('status', 1);
  }

  public function wishlists()
  {
    return $this->hasMany(Wishlist::class);
  }

  public function stocks()
  {
    return $this->hasMany(ProductStock::class);
  }
  public function translate(?string $locale = null, bool $withFallback = false): ?Model
  {
    return $this->getTranslation($locale, $withFallback);
  }
  public function translations(): HasMany
  {
    return $this->hasMany($this->getTranslationModelName(), $this->getTranslationRelationKey());
  }
}
