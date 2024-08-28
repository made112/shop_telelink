<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductStock extends Model
{
    //
    public function product(){
    	return $this->belongsTo(Product::class);
    }

    public function getPriceAttribute($value)
    {
        if ($this->getOriginal('discount') > 0) {
            $Original = $this->getOriginal('discount');
            return $Original;
        }
        return $value;

    }
}
