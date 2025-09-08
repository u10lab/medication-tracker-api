<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Medication extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'medications';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'image_path',
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
        'is_active',
        'schedule'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'indications' => 'array',
        'contraindications' => 'array',
        'side_effects' => 'array',
        'drug_interactions' => 'array',
        'is_active' => 'boolean',
        'schedule' => 'array'
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
     * Scope a query for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
