<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Category;

class Product extends Model
{
    use HasFactory;

    protected $fiilable = ['title' , 'description' , 'price' , 'category_id'];

    public function category(): hasMany{
        return $this->belongsTo(Category::class);
    }
}
