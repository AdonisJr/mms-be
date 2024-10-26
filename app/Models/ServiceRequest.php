<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceRequest extends Model
{

    use HasFactory;

    protected $fillable = [
        'service_id', 
        'requested_by', 
        'approved_by', 
        'status',
        'description', 
        'expected_start_date', 
        'expected_end_date', 
        'number_of_personnel', 
        'classification'
    ];

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function requested()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
    
    public function tasks()
    {
        return $this->hasMany(Task::class, 'service_request_id');
    }
}
