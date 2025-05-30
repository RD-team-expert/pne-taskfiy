<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'workspace_id',
        'user_id',
        'invoice_id',
        'payment_method_id',
        'amount',
        'payment_date',
        'note',
        'created_by'
    ];

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    public function invoice()
    {
        return $this->belongsTo(EstimatesInvoice::class, 'invoice_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
