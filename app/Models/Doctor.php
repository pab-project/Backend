<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'specialization', 'phone', 'bio'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function timeSlots()
    {
        return $this->hasMany(TimeSlot::class);

    }
    
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

}
