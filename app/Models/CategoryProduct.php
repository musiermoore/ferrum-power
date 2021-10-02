<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryProduct extends Model
{
    use HasFactory;

    public $timestamps = false;

    const DEFAULT_CATEGORY_ID = 1;
    const USED_GOODS_CATEGORY_ID = 2;

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

    public static function isImmutableCategory($id)
    {
        if ($id == self::DEFAULT_CATEGORY_ID || $id == self::USED_GOODS_CATEGORY_ID) {
            return true;
        }

        return false;
    }
}
