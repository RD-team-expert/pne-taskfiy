<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Allowance extends Model
{
    use HasFactory;
    protected $fillable = [
        'workspace_id',
        'title',
        'amount'
    ];

    public function payslips()
    {
        return $this->belongsToMany(Payslip::class)->where('payslips.workspace_id', getWorkspaceId());
    }
}
