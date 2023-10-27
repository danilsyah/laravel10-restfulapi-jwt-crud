<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    // field yang boleh di insert/input
    protected $fillable = [
        'user_id',
        'title',
        'content',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
