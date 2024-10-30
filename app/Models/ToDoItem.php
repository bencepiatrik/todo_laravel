<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ToDoItem extends Model
{
    //


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function sharedUsers()
    {
        return $this->hasMany(SharedItem::class);
    }
}
