<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Product;

class Category extends Model
{
    use HasFactory;

    protected $fiilable = ['title'];

    public function products(): hasMany{
        return $this->hasMany(Product::class);
    }
}
