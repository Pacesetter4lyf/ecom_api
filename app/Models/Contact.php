<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'phone',
        'firstname',
        'lastname',
        'address',
        'suit_no',
        'city',
        'country',
        'postal_code',
        'contact_me'
    ];

    public function owner(){
        return $this->belongsTo(User::class, 'user_id')->select('id', 'email');
    }
}


// public function relatedCourses(){

//     return $this->belongsTo(Courseupload::class, "courseID");

// }
