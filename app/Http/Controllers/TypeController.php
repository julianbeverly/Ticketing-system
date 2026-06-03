<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IncidentType;

class TypeController extends Controller
{
    /**
     * Store a newly created incident type in the database.
     * Links the type to a parent category and saves the incident name.
     */
    public function store(Request $request)
    {
        $request->validate(['name' => 'required', 'category_id' => 'required']);
        IncidentType::create([
            'name' => $request->name,
            'category_id' => $request->category_id,
        ]);
        return redirect()->back()->with('success', 'Type created successfully');
    }

    /**
     * Update the existing incident type in the database.
     * Allows changing the incident name or reassigning it to a different category.
     */
    public function update(Request $request, IncidentType $type)
    {
        $request->validate(['name' => 'required', 'category_id' => 'required']);
        $type->update([
            'name' => $request->name,
            'category_id' => $request->category_id,
        ]);
        return redirect()->back()->with('success', 'Type updated successfully');
    }

    /**
     * Delete the specified incident type from the database.
     * Triggers a deletion of the record and returns a success notification.
     */
    public function destroy(IncidentType $type)
    {
        $type->delete();
        return redirect()->back()->with('success', 'Type deleted successfully');
    }
}
