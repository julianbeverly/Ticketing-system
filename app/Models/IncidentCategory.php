<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncidentCategory extends Model
{
    // Define the database table associated with this model
    protected $table = 'categories';
    
    // Allow mass-assignment for name and description fields
    protected $fillable = ['name', 'description'];

    /**
     * Define the relationship between Category and its Types.
     * A single category can have multiple incident types associated with it.
     */
    public function incidentTypes()
    {
        return $this->hasMany(IncidentType::class, 'category_id');
    }
}
