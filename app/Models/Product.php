<?php

namespace App\Models;

use App\Http\Controllers\UserController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'item_sold',
        'current_price',
        'former_price',
        'color',
        'short_description',
        'long_description',
        'category',
        'tags',
        'features',
        'image',
        'sm_images_id',
        'size',
        'is_featured',
        'is_latest',
        'is_unique',
        'is_trending',
        'discount',
        'brand',
        'code',
        'type'
    ];


    public function user_like()
    {
        $user = new UserController;
        $user_id = $user->getAuthenticatedUser()->original['user']->id;
        // return $user_id;
        return $this->belongsToMany(User::class, 'favourites')->withPivot('isFavourite')->wherePivot('user_id', $user_id);//->as('subscriptions')
    }


}
