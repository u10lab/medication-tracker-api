<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class MedicationPattern extends Model
{
    use HasFactory, HasUuids;

    /**
     * The table associated with the model.
     */
    protected $table = 'medication_patterns';

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
        'pattern_name',
        'schedule_type',
        'days_of_week',
        'times_per_day',
        'dosage_amount',
        'dosage_unit',
        'interval_hours',
        'start_date',
        'end_date',
        'is_active',
        'notes',
        'specific_times'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'days_of_week' => 'array',
        'specific_times' => 'array',
        'dosage_amount' => 'decimal:3',
        'times_per_day' => 'integer',
        'interval_hours' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the user that owns the medication pattern.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the medication this pattern belongs to.
     */
    public function medication(): BelongsTo
    {
        return $this->belongsTo(Medication::class);
    }

    /**
     * Get the medication logs for this pattern.
     */
    public function medicationLogs(): HasMany
    {
        return $this->hasMany(MedicationLog::class);
    }

    /**
     * Scope a query to only include active patterns.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query for a specific schedule type.
     */
    public function scopeByScheduleType($query, $type)
    {
        return $query->where('schedule_type', $type);
    }
}
