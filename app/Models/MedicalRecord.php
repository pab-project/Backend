<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalRecord extends Model
{
    protected $fillable = [
        'appointment_id',
        'patient_id',
        'doctor_id',
        'diagnosis',
        'treatment',
        'medications',
        'notes'
    ];

    protected $casts = [
        'medications' => 'array'
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}
