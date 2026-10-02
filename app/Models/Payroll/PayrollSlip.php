<?php

namespace App\Models\Payroll;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollSlip extends Model
{
    use HasFactory;
    protected $table = 'payroll_slips';
    protected $connection = 'db_payslip';
    protected $fillable = [
        'payroll_period_id',
        'payroll_period_employee_id',

        'file_name',

        'encrypted_file',

        'original_file_size',
        'encrypted_file_size',

        'mime_type',

        'encryption_algorithm',
        'encryption_version',

        'encryption_salt',
        'encryption_iv',
        'encryption_tag',

        'status',

        'uploaded_by',
        'uploaded_at',
    ];

    protected $casts = [
        'original_file_size' => 'integer',
        'encrypted_file_size' => 'integer',
        'uploaded_at' => 'datetime',
    ];

    protected $hidden = [
        'encrypted_file',
        'encryption_iv',
        'encryption_tag',
    ];

    public function period()
    {
        return $this->belongsTo(
            PayrollPeriod::class,
            'payroll_period_id'
        );
    }

    public function employee()
    {
        return $this->belongsTo(
            PayrollPeriodEmployee::class,
            'payroll_period_employee_id'
        );
    }
}