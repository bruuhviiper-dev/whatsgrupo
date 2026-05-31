<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'blog_category_id',
        'title',
        'slug',
        'content',
        'meta_description',
        'views',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'views'        => 'integer',
    ];

    public function blogCategory()
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }
}
