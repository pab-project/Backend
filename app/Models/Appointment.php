<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'patient_id','doctor_id','time_slot_id','status','notes'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function timeSlot()
    {
        return $this->belongsTo(TimeSlot::class);
    }

    public function medicalRecord()
    {
        return $this->hasOne(MedicalRecord::class);
    }
}
