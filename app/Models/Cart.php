<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $table = 'carts';
    protected $guarded = ['id'];

    public function category()
    {
        return $this->hasOne(Category::class, 'id', 'category_id');
    }
}
