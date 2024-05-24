<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = ['category_id', 'title', 'author', 'edition', 'isbn', 'quantity', 'description', 'self_number'];

    // public function borrows()
    // {
    //     return $this->hasMany(Borrow::class);S
    // }


    public function category()
    {
        return $this->belongsTo(Category::class);
    }






}
