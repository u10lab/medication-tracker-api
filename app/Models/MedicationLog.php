<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicationLog extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'medication_logs';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'medication_id',
        'scheduled_date',
        'scheduled_time',
        'actual_time',
        'status',
        'side_effects',
        'notes',
        'severity_level'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'scheduled_date' => 'date',
        'scheduled_time' => 'datetime',
        'actual_time' => 'datetime',
        'side_effects' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the medication this log belongs to.
     */
    public function medication(): BelongsTo
    {
        return $this->belongsTo(Medication::class);
    }

    /**
     * Scope a query for a specific status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query for a specific date range.
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('scheduled_date', [$startDate, $endDate]);
    }

    /**
     * Scope a query for taken medications.
     */
    public function scopeTaken($query)
    {
        return $query->where('status', 'taken');
    }

    /**
     * Scope a query for missed medications.
     */
    public function scopeMissed($query)
    {
        return $query->where('status', 'missed');
    }
}
