<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = ['name'];

    public function blogs()
    {
        return $this->belongsToMany(BlogPost::class, 'blog_tag', 'blog_post_id');
    }
}
