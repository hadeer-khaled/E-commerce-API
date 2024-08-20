<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use HasFactory;

    protected $fillable = ['url','original_filename','storage_filename', 'attachable_id', 'attachable_type'];


    public function attachable(){
        return $this->morphTo();
    }
}
