<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['category_id', 'name', 'base_image', 'shadow_overlay'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function gridPresets()
    {
        return $this->belongsToMany(GridPreset::class, 'product_grid');
    }
}
