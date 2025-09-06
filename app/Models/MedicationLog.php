<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class MedicationLog extends Model
{
    use HasFactory, HasUuids;

    /**
     * The table associated with the model.
     */
    protected $table = 'medication_logs';

    /**
     * The primary key type.
     */
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'medication_id',
        'medication_pattern_id',
        'scheduled_time',
        'taken_time',
        'dosage_amount',
        'dosage_unit',
        'status',
        'notes',
        'side_effects',
        'effectiveness_rating'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'scheduled_time' => 'datetime',
        'taken_time' => 'datetime',
        'dosage_amount' => 'decimal:3',
        'side_effects' => 'array',
        'effectiveness_rating' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the user that owns the medication log.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the medication this log belongs to.
     */
    public function medication(): BelongsTo
    {
        return $this->belongsTo(Medication::class);
    }

    /**
     * Get the medication pattern this log belongs to.
     */
    public function medicationPattern(): BelongsTo
    {
        return $this->belongsTo(MedicationPattern::class);
    }

    /**
     * Scope a query for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
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
        return $query->whereBetween('scheduled_time', [$startDate, $endDate]);
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
