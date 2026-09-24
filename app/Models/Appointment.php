<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'user_id', 'doctor_id', 'organization_id', 'appointment_date',
        'appointment_time', 'status', 'notes', 'payment_status',
        'amount', 'booking_fee', 'total_amount', 'transaction_id'
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'appointment_time' => 'datetime',
        'cancelled_at' => 'datetime',
        'completed_at' => 'datetime'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function rating()
    {
        return $this->hasOne(Rating::class);
    }

    //upcoming appointments

    public function scopeUpcoming($query)
    {
        return $query->where('appointment_date', '>', now()->toDateString())->where('status', 'booked');
    }   
    //past appointments
    public function scopePast($query)
    {
        return $query->where('appointment_date', '<', now());
    }
}
