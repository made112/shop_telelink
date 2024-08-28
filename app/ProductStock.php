<?php

namespace App;

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
            return $this->getOriginal('discount');
        }
        return $value;
    }
}
