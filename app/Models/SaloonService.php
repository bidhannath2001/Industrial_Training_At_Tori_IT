<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaloonService extends Model
{
    protected $fillable = ['organization_id', 'service_name', 'price', 'duration_minutes', 'description'];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function appointments()
    {
        return $this->hasMany(SaloonAppointment::class, 'service_id');
    }
}
