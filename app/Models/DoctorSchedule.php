<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoctorSchedule extends Model
{
    protected $fillable = [
        'doctor_id',
        'schedule_date',
        'shift',
        'start_time',
        'end_time',
        'is_active',
        'notes',
        'order',
    ];

    protected $casts = [
        'schedule_date' => 'date',
        'is_active' => 'boolean',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];
    
    /**
     * Get shift label in Indonesian
     */
    public function getShiftLabelAttribute(): string
    {
        return match($this->shift) {
            'shift_1' => 'Shift 1 (Pagi - Sore)',
            'shift_2' => 'Shift 2 (Malam - Dini Hari)',
            default => 'Tidak Ada Shift',
        };
    }

    /**
     * Get the doctor that owns the schedule.
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    /**
     * Scope for active schedules only.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by custom order field.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
