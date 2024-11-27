<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{

    use HasFactory;

    protected $fillable = [
        'name',
        'user_id',
        'equipment_type',
        'model',
        'acquisition_date',
        'location',
        'warranty',
        'department',
        'status',
        'condition',
        'health',
    ];

    public function requested()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
}
