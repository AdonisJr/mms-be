<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['service_request_id', 'deadline', 'status', 'proof'];

    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class, 'service_request_id');
    }

   // Many-to-many relationship for assigned users
   public function utilityWorkers()
   {
       return $this->belongsToMany(User::class, 'task_user');
   }
    
}
