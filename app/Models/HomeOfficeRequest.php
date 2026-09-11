<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeOfficeRequest extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'scheduled_start_date' => 'date',
        'scheduled_end_date' => 'date',
        'checkout_at' => 'datetime',
        'checkin_at' => 'datetime',
        'accessories' => 'array',
        'policy_accepted' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function checkoutAgent()
    {
        return $this->belongsTo(User::class, 'checkout_by');
    }

    public function checkinAgent()
    {
        return $this->belongsTo(User::class, 'checkin_by');
    }
}
