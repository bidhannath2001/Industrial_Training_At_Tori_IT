<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    
    protected $fillable = [
        'name',
        'email',
        'phone',
        'gender',
        'age',
        'state',
        'district_id',
        'village_name',
        'height',
        'weight',
        'avatar',
        'password',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected $casts = [
        'email_verified_at'=> 'datetime',
        'is_active' => 'boolean'
    ];

    //Relationship
    public function appointments(){
        return $this->hasMany(Appointment::class);
    }
    public function ratings(){
        return $this->hasMany(Rating::class);
    }
    public function saloonAppointments(){
        return $this->hasMany(SaloonAppointment::class);
    }
    
}
