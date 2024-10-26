<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreventiveMaintenance extends Model
{
    use HasFactory;

    protected $table = 'preventive_maintenance';

    // Define the fields that are mass assignable
    protected $fillable = [
        'name', 
        'description', 
        'scheduled_date_from', 
        'scheduled_date_to', 
        'status', 
        'created_by'
    ];

    // Relationship to the User model (creator of the task)
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'preventive_maintenance_user');
    }

    // Customize the array output of the model
    public function toArray()
    {
        $array = parent::toArray();
        
        // Add users to the array in a readable format
        $array['users'] = $this->users->map(function ($user) {
            return $user->toArray();
        });
        
        return $array;
    }
}
