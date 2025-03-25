<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    protected $fillable = [
        'title', 'slug', 'category', 'author', 'content', 'image', 'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'author');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function getImageAttribute($image)
    {
        if ($image) {
            return url("storage/{$image}");
        }
    }
}
