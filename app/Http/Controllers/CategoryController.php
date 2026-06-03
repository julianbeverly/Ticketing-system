<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IncidentCategory;

class CategoryController extends Controller
{
    /**
     * Store a newly created incident category in the database.
     * Validates the request and saves the category name and description.
     */
    public function store(Request $request)
    {
        $request->validate(['name' => 'required']);
        IncidentCategory::create([
            'name' => $request->name,
            'description' => $request->description
        ]);
        return redirect()->back()->with('success', 'Category created successfully');
    }

    /**
     * Update the specified incident category in the database.
     * Updates the name and description based on the provided request data.
     */
    public function update(Request $request, IncidentCategory $category)
    {
        $request->validate(['name' => 'required']);
        $category->update([
            'name' => $request->name,
            'description' => $request->description
        ]);
        return redirect()->back()->with('success', 'Category updated successfully');
    }

    /**
     * Remove the specified incident category from the database.
     * Deletes the category record and returns to the previous page.
     */
    public function destroy(IncidentCategory $category)
    {
        $category->delete();
        return redirect()->back()->with('success', 'Category deleted successfully');
    }
}
