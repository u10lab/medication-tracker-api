<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMedicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'generic_name' => 'nullable|string|max:255',
            'dosage_form' => 'nullable|string|max:100',
            'strength' => 'nullable|string|max:100',
            'manufacturer' => 'nullable|string|max:255',
            'prescription_number' => 'nullable|string|max:100',
            'prescribing_doctor' => 'nullable|string|max:255',
            'pharmacy' => 'nullable|string|max:255',
            'ndc_number' => 'nullable|string|max:50',
            'indications' => 'nullable',
            'contraindications' => 'nullable',
            'side_effects' => 'nullable',
            'drug_interactions' => 'nullable',
            'storage_instructions' => 'nullable|string',
            'notes' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'description' => 'nullable|string',
            'image_path' => 'nullable|string|max:500',
            // スケジュール情報
            'schedule' => 'nullable|array',
            'schedule.type' => 'nullable|string',
            'schedule.dosesPerDay' => 'nullable|integer',
            'schedule.times' => 'nullable|array',
            'schedule.times.*' => 'nullable|string',
            'schedule.startDate' => 'nullable|date',
            'schedule.endDate' => 'nullable|date|after_or_equal:schedule.startDate',
            'schedule.cyclePattern' => 'nullable|array',
            'image' => 'nullable',
        ];
    }
}