<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GridPreset extends Model
{
    protected $fillable = ['label', 'colss', 'rowss', 'scale_value'];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_grid');
    }
}
