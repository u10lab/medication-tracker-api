<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class SideEffectType extends Model
{
    use HasFactory, HasUuids;

    /**
     * The table associated with the model.
     */
    protected $table = 'side_effect_types';

    /**
     * The primary key type.
     */
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'description',
        'severity_level',
        'category',
        'is_common',
        'requires_medical_attention',
        'symptoms'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'symptoms' => 'array',
        'is_common' => 'boolean',
        'requires_medical_attention' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Scope a query for common side effects.
     */
    public function scopeCommon($query)
    {
        return $query->where('is_common', true);
    }

    /**
     * Scope a query for side effects requiring medical attention.
     */
    public function scopeRequiringMedicalAttention($query)
    {
        return $query->where('requires_medical_attention', true);
    }

    /**
     * Scope a query for a specific severity level.
     */
    public function scopeBySeverity($query, $severity)
    {
        return $query->where('severity_level', $severity);
    }

    /**
     * Scope a query for a specific category.
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }
}
