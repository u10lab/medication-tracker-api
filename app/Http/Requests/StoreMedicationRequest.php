<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMedicationRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'generic_name' => 'nullable|string|max:255',
            'dosage_form' => 'required|string|max:100',
            'strength' => 'nullable|string|max:100',
            'manufacturer' => 'nullable|string|max:255',
            'prescription_number' => 'nullable|string|max:100',
            'prescribing_doctor' => 'nullable|string|max:255',
            'pharmacy' => 'nullable|string|max:255',
            'ndc_number' => 'nullable|string|max:50',
            'indications' => 'nullable|array',
            'indications.*' => 'string|max:500',
            'contraindications' => 'nullable|array',
            'contraindications.*' => 'string|max:500',
            'side_effects' => 'nullable|array',
            'side_effects.*' => 'string|max:500',
            'drug_interactions' => 'nullable|array',
            'drug_interactions.*' => 'string|max:500',
            'storage_instructions' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:2000',
            'is_active' => 'boolean'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => '薬剤名は必須です',
            'name.max' => '薬剤名は255文字以内で入力してください',
            'dosage_form.required' => '剤形は必須です',
            'dosage_form.max' => '剤形は100文字以内で入力してください',
            'indications.*.max' => '適応症は各項目500文字以内で入力してください',
            'contraindications.*.max' => '禁忌は各項目500文字以内で入力してください',
            'side_effects.*.max' => '副作用は各項目500文字以内で入力してください',
            'drug_interactions.*.max' => '薬物相互作用は各項目500文字以内で入力してください'
        ];
    }
}
