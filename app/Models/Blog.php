<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;
    protected $fillable = [
        'banner',
        'banner_aux',
        'title',
        'short_desc',
        'category',
        'is_recent',
        'tags',
        'content',
        'special_text',
        'static_img',
        'video_img',
    ];
}
