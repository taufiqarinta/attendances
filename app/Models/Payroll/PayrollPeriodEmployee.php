<?php

namespace App\Models\Payroll;

use App\Models\Payroll\PayrollSlip;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollPeriodEmployee extends Model
{
    use HasFactory;

    protected $connection = 'db_payslip';
    protected $table = 'payroll_period_employees';

    protected $fillable = [
        'payroll_period_id',

        'nik',
        'nama',
        'email',

        'level',
        'plant',
        'comp',

        'tglmasuk',

        'divisi',
        'dept',

        'jabatan',
        'kode_jabatan',

        'role',
    ];

    protected $casts = [
        'tglmasuk' => 'date',
    ];

    public function period()
    {
        return $this->belongsTo(
            PayrollPeriod::class,
            'payroll_period_id'
        );
    }

    public function slip()
    {
        return $this->hasOne(
            PayrollSlip::class,
            'payroll_period_employee_id'
        );
    }
}