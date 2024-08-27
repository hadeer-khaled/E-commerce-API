<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

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

    protected function title(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => ucwords($value), // Accessor
            set: fn (string $value) => strtolower($value), // Mutator
        );
    }
}
