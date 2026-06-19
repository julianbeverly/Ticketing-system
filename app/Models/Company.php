<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    // Define the database table associated with this model
    protected $table = 'companies';
    
    // Allow mass-assignment for name and description fields
    protected $fillable = ['name', 'description'];

    /**
     * Define the relationship between Category and its Types.
     * A single category can have multiple incident types associated with it.
     */
    public function departments()
    {
        return $this->hasMany(Department::class, 'company_id');
    }
}
