<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaloonAppointment extends Model
{
    protected $fillable = [
        'user_id', 'organization_id', 'service_id', 'appointment_date',
        'appointment_time', 'status', 'payment_status', 'amount'
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'appointment_time' => 'datetime',
        'cancelled_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function service()
    {
        return $this->belongsTo(SaloonService::class, 'service_id');
    }
}
