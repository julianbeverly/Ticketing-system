<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncidentType extends Model
{
    // Specify the table name for incident types
    protected $table = 'types';
    
    // Define which fields can be filled during mass assignment
    protected $fillable = ['category_id', 'name'];

    /**
     * Define the inverse relationship to the parent category.
     * Each incident type belongs to exactly one category.
     */
    public function category()
    {
        return $this->belongsTo(IncidentCategory::class, 'category_id');
    }
}
