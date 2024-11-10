<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\HasMany;


use App\Models\Product;
use App\Models\Attachment;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['title'];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function attachment(): MorphOne
    {
        return $this->morphOne(Attachment::class, 'attachable');
    }

    public function scopeCustomPaginate($query, $offset, $limit)
    {
        return $query->offset($offset)->limit($limit);
    }

}
