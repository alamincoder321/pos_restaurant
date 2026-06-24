<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SalaryDetail extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = false;

    protected $guarded = ['id'];

    public function employee(){
        return $this->belongsTo(User::class, 'employee_id', 'id')->select('id', 'name', 'emp_code', 'department_id', 'designation_id', 'phone', 'email')->with('department', 'designation')->withTrashed();
    }
}
