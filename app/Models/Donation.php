<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Donation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'donation_category_id',
        'activity_id',
        'donor_name',
        'donor_email',
        'donor_phone',
        'amount',
        'note',
        'payment_method',
        'payment_status',
        'verification_status',
        'midtrans_order_id',
        'midtrans_transaction_id',
        'payment_payload',
        'bank_transfer_proof_path',
        'paid_at',
        'verified_by',
        'verified_at',
        'rejection_reason',
        'receipt_number',
        'receipt_pdf_path',
        'donated_at',
    ];

    protected $casts = [
        'payment_payload' => 'array',
        'paid_at' => 'datetime',
        'verified_at' => 'datetime',
        'donated_at' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(DonationCategory::class, 'donation_category_id');
    }

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
