<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PreventiveMaintenanceReport;

class PreventiveMaintenanceReportController extends Controller
{
    public function index()
    {
        $reports = PreventiveMaintenanceReport::with(['preventiveMaintenance', 'equipment'])->get();
        return response()->json($reports);
    }

    public function store(Request $request)
    {
        $request->validate([
            'preventive_id' => 'required|exists:preventive_maintenance,id',
            'inventory_id' => 'required|exists:inventories,id',
            'condition' => 'required|string',
            'health' => 'required|integer',
            'other_info' => 'nullable|string',
        ]);

        $report = PreventiveMaintenanceReport::create($request->all());

        return response()->json($report, 201);
    }

    public function show($id)
    {
        $report = PreventiveMaintenanceReport::with(['preventiveMaintenance', 'equipment'])->find($id);

        if (!$report) {
            return response()->json(['message' => 'Report not found'], 404);
        }

        return response()->json($report);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'condition' => 'required|string',
            'health' => 'required|integer',
            'other_info' => 'nullable|string',
        ]);

        $report = PreventiveMaintenanceReport::find($id);

        if (!$report) {
            return response()->json(['message' => 'Report not found'], 404);
        }

        $report->update($request->all());

        return response()->json($report);
    }

    public function destroy($id)
    {
        $report = PreventiveMaintenanceReport::find($id);

        if (!$report) {
            return response()->json(['message' => 'Report not found'], 404);
        }

        $report->delete();

        return response()->json(['message' => 'Report deleted successfully']);
    }

     // New method to fetch reports by preventive_id
     public function getReportsByPreventiveId($preventiveId)
     {
         $reports = PreventiveMaintenanceReport::with(['preventiveMaintenance', 'equipment'])
             ->where('preventive_id', $preventiveId)
             ->get();
 
         if ($reports->isEmpty()) {
             return response()->json([], 200);
         }
 
         return response()->json($reports);
     }
     
}
