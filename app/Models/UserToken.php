<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserToken extends Model
{

    use HasFactory;

    // Define which attributes can be mass-assigned
    protected $fillable = [
        'user_id',
        'expo_push_token',
    ];

    // Define the relationship with the User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Define the relationship with UserToken model
    public function userTokens()
    {
        return $this->hasMany(UserToken::class);
    }
}
