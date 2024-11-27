<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreventiveMaintenanceReport extends Model
{

    use HasFactory;

    protected $table = 'preventive_maintenance_report'; // Explicitly set the table name

    protected $fillable = [
        'preventive_id',
        'service_request_id',
        'condition',
        'health',
        'other_info',
    ];

    // Relationships
    public function preventiveMaintenance()
    {
        return $this->belongsTo(PreventiveMaintenance::class, 'preventive_id');
    }

    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class, 'service_request_id');
    }
}
