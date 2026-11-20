<?php

namespace App\Models\Payroll;

use App\Models\Payroll\PayrollPeriodEmployee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollPeriod extends Model
{
    use HasFactory;
    protected $connection = 'db_payslip';
    protected $table = 'payroll_periods';

    protected $fillable = [
        'period_code',
        'period_name',
        'period_year',
        'period_month',
        'status',
        'total_employees',
        'total_uploaded',
        'created_by',
    ];

    protected $casts = [
        'period_year' => 'integer',
        'period_month' => 'integer',
        'total_employees' => 'integer',
        'total_uploaded' => 'integer',
    ];

    public function employees()
    {
        return $this->hasMany(
            PayrollPeriodEmployee::class,
            'payroll_period_id'
        );
    }

    public function slips()
    {
        return $this->hasMany(
            PayrollSlip::class,
            'payroll_period_id'
        );
    }
}