<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    //
    protected $fillable = [
        'user_id', 'organization_id', 'registration_number', 'designation',
        'higher_degree', 'subcategory_id', 'image', 'bio', 'status',
        'experience_years', 'fee', 'is_available', 'avg_delay_minutes',
        'additional_time_minutes', 'available_days', 'starting_time',
        'ending_time', 'break_time_start', 'break_time_end'
    ];

    protected $casts = [
        'is_available' => 'boolean'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    //Get only approaved doctors
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
    //Get average rating
    public function getAverageRating()
    {
        return $this->ratings()->avg('rating')??0;
    }
    //check if available on specific date
    public function isAvailableOn($date){
        $dayName = date('D', strtotime($date));
        $availableDays = explode(',', str_replace(' ', '', $this->availableDays));
        return in_array($dayName, $availableDays);
    }
}
