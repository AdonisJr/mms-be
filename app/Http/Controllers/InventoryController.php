<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inventory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log; // Import Log facade

class InventoryController extends Controller
{
    /**
     * Display a listing of the inventories.
     */
    public function index()
    {
        $inventories = Inventory::with([
            'requested'
        ])->get();

        return response()->json($inventories);
    }

    /**
     * Store a newly created inventory in storage.
     */

    public function store(Request $request)
    {
        $request->validate([
            'equipment_type' => 'required|string',
            'model' => 'nullable|string',
            'acquisition_date' => 'required|date',
            'location' => 'nullable|string',
            'warranty' => 'nullable|string',
            'department' => 'nullable|string',
            'status' => 'required|string',
            'condition' => 'required|string',
            'health' => 'required|integer'
        ]);
    
        // Use Carbon to format the acquisition_date
        $request->merge([
            'acquisition_date' => Carbon::parse($request->acquisition_date)->format('Y-m-d')
        ]);
    
        $inventory = Inventory::create($request->all());
        return response()->json($inventory, 201);
    }
    

    /**
     * Display the specified inventory.
     */
    public function show($id)
    {
        $inventory = Inventory::find($id);

        if (!$inventory) {
            return response()->json(['message' => 'Inventory not found'], 404);
        }

        return response()->json($inventory);
    }

    /**
     * Update the specified inventory in storage.
     */
    public function update(Request $request, $id)
{
    $request->validate([
        'equipment_type' => 'required|string',
        'user_id' => 'nullable|integer',
        'name' => 'required|string',
        'model' => 'nullable|string',
        'acquisition_date' => 'required|date',
        'location' => 'nullable|string',
        'warranty' => 'nullable|string',
        'department' => 'nullable|string',
        'status' => 'required|string',
        'condition' => 'required|string',
        'health' => 'required|integer'
    ]);

    Log::info('Update Request Payload', $request->all());

    $inventory = Inventory::find($id);

    if (!$inventory) {
        return response()->json(['message' => 'Inventory not found'], 404);
    }

    // Update explicitly to ensure `user_id` is handled correctly
    $inventory->equipment_type = $request->equipment_type;
    $inventory->user_id = $request->user_id; // Set user_id explicitly
    $inventory->name = $request->name;
    $inventory->model = $request->model;
    $inventory->acquisition_date = $request->acquisition_date;
    $inventory->location = $request->location;
    $inventory->warranty = $request->warranty;
    $inventory->department = $request->department;
    $inventory->status = $request->status;
    $inventory->condition = $request->condition;
    $inventory->health = $request->health;

    $inventory->save();

    return response()->json($inventory);
}


    /**
     * Remove the specified inventory from storage.
     */
    public function destroy($id)
    {
        $inventory = Inventory::find($id);

        if (!$inventory) {
            return response()->json(['message' => 'Inventory not found'], 404);
        }

        $inventory->delete();
        return response()->json(['message' => 'Inventory deleted successfully']);
    }
}
