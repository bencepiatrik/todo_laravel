<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class ToDoItem extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'category_id',
        'user_id',
        'is_done',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function sharedWith()
    {
        return $this->belongsToMany(User::class, 'shared_items');
    }

    public function sharedItems()
    {
        return $this->hasMany(SharedItem::class, 'to_do_item_id');
    }
    public function sharedUsers()
    {
        return $this->belongsToMany(User::class, 'shared_items', 'to_do_item_id', 'shared_with_id');
    }
}
