<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryProduct extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'parent_id',
        'title',
        'slug',
        'description',
        'image_path',
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }
}
