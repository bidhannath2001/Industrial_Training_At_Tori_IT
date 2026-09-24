<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    
    protected $fillable = [
        'name', 'type', 'owner_name', 'email', 'phone', 'location', 'district_id',
        'state', 'description', 'banner_image', 'logo', 'is_approved',
        'gst_amount', 'base_amount', 'commission', 'zapmor_commission', 'booking_fee'
    ];
    protected $casts = [
        'is_approved' => 'boolean'
    ];
    public function district(){
        return $this->belongsTo(District::class);
    }
    public function doctors(){
        return $this->hasMany(Doctor::class);
    }
    public function saloonServices(){
        return $this->hasMany(SaloonService::class);
    }
    public function appointments(){
        return $this->hasMany(Appointment::class);
    }
    public function saloonAppointments(){
        return $this->hasMany(SaloonAppointment::class);
    }
}
