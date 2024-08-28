<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Brand extends Model
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
}
