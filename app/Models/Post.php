<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $hidden = [
        "created_at",
        "updated_at",

    ];

    protected $fillable=[
        'title',
        'content',
        'category_id',
        'featured_image',
    ];
    protected $casts = [
        'vote_up_ids' => 'array',
        'vote_down_ids' => 'array',
    ];
    public function comments(){
        return $this->hasMany(Comment::class,'post_id','id');
    }
    public function category(){
        return $this->belongsTo(Category::class,'category_id','id');
    }
    public function user(){
        return $this->belongsTo(User::class);
    }

}
