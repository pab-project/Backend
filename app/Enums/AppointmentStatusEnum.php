<?php

namespace App\Enums;

enum AppointmentStatusEnum: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
}
