<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{

    use HasFactory;

    protected $fillable = [
        'name',
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
    
}
