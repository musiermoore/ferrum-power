<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'price',
        'stock_availability',
        'description',
        'image_path',
    ];

    public function category()
    {
        return $this->belongsTo(CategoryProduct::class, 'category_id', 'id');
    }

    public static function setDefaultCategoryForProduct($categoryId)
    {
        Product::where('category_id', $categoryId)->update(['category_id' => CategoryProduct::DEFAULT_CATEGORY_ID]);
    }
}
