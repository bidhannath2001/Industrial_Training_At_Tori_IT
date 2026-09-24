<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    protected $fillable = ['name','state'];
    public function organizations(){
        return $this->hasMany(Organization::class);
    }
    public function users(){
        return $this->hasMany(User::class);
    }
}
