<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{

    use HasFactory;

    protected $fillable = ['service_request_id', 'user_id', 'comment'];

    public function serviceRequest() {
        return $this->belongsTo(ServiceRequest::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
