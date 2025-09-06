<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MedicationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'generic_name' => $this->generic_name,
            'dosage_form' => $this->dosage_form,
            'strength' => $this->strength,
            'manufacturer' => $this->manufacturer,
            'prescription_number' => $this->prescription_number,
            'prescribing_doctor' => $this->prescribing_doctor,
            'pharmacy' => $this->pharmacy,
            'ndc_number' => $this->ndc_number,
            'indications' => $this->indications ?? [],
            'contraindications' => $this->contraindications ?? [],
            'side_effects' => $this->side_effects ?? [],
            'drug_interactions' => $this->drug_interactions ?? [],
            'storage_instructions' => $this->storage_instructions,
            'notes' => $this->notes,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            
            // Include related data when loaded
            'patterns' => $this->whenLoaded('medicationPatterns'),
            'logs' => $this->whenLoaded('medicationLogs'),
            
            // Include counts when available
            'patterns_count' => $this->whenCounted('medicationPatterns'),
            'logs_count' => $this->whenCounted('medicationLogs'),
        ];
    }
}
