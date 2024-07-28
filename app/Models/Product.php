<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Models\Category;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['title' , 'description' , 'price' , 'category_id'];

    public function category(): BelongsTo{
        return $this->belongsTo(Category::class);
    }

    public function attachments(): MorphMany{
        return $this->morphMany(Attachment::class , 'attachable');
    }


}
