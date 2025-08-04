<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class employees extends Model
{


    // Optional: explicitly define the table name if it differs from the model name
    protected $table = 'employees';

    // Optional: define which fields are mass assignable
    protected $fillable = ['name', 'age', 'salary'];


}
