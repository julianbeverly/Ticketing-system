<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    // Specify the table name for incident types
    protected $table = 'departments';
    
    // Define which fields can be filled during mass assignment
    protected $fillable = ['company_id', 'name'];

    /**
     * Define the inverse relationship to the parent category.
     * Each incident type belongs to exactly one category.
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
