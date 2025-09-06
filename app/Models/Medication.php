<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Medication extends Model
{
    use HasFactory, HasUuids;

    /**
     * The table associated with the model.
     */
    protected $table = 'medications';

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
        'name',
        'generic_name',
        'dosage_form',
        'strength',
        'manufacturer',
        'prescription_number',
        'prescribing_doctor',
        'pharmacy',
        'ndc_number',
        'indications',
        'contraindications',
        'side_effects',
        'drug_interactions',
        'storage_instructions',
        'notes',
        'is_active'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'indications' => 'array',
        'contraindications' => 'array',
        'side_effects' => 'array',
        'drug_interactions' => 'array',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the user that owns the medication.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the medication patterns for this medication.
     */
    public function medicationPatterns(): HasMany
    {
        return $this->hasMany(MedicationPattern::class);
    }

    /**
     * Get the medication logs for this medication.
     */
    public function medicationLogs(): HasMany
    {
        return $this->hasMany(MedicationLog::class);
    }

    /**
     * Scope a query to only include active medications.
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
}
