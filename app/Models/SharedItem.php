<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SharedItem extends Model
{
    use HasFactory;

    protected $fillable = ['to_do_item_id', 'owner_id', 'shared_with_id', 'created_at', 'updated_at'];

    public function toDoItem()
    {
        return $this->belongsTo(ToDoItem::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function sharedWith()
    {
        return $this->belongsTo(User::class, 'shared_with_id');
    }
}
