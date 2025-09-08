<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMedicationRequest;
use App\Http\Requests\UpdateMedicationRequest;
use App\Http\Resources\MedicationResource;
use App\Http\Resources\MedicationCollection;
use App\Models\Medication;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class MedicationController extends Controller
{

    /**
     * Display a listing of the user's medications.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            
            $query = Medication::forUser($user->id);

            // Search by name
            if ($request->has('search')) {
                $search = $request->get('search');
                $query->where('name', 'like', "%{$search}%");
            }

            $medications = $query->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $medications->items(),
                'meta' => [
                    'current_page' => $medications->currentPage(),
                    'last_page' => $medications->lastPage(),
                    'per_page' => $medications->perPage(),
                    'total' => $medications->total()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve medications',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created medication.
     */
    public function store(StoreMedicationRequest $request): JsonResponse
    {
        try {
            $user = Auth::user();
            \Log::info('Creating medication for user:', ['user_id' => $user->id, 'data' => $request->all()]);
            
            $medication = Medication::create([
                'user_id' => $user->id,
                'name' => $request->name,
                'description' => $request->description,
                'image_path' => $request->image_path,
                'generic_name' => $request->generic_name,
                'dosage_form' => $request->dosage_form,
                'strength' => $request->strength,
                'manufacturer' => $request->manufacturer,
                'prescription_number' => $request->prescription_number,
                'prescribing_doctor' => $request->prescribing_doctor,
                'pharmacy' => $request->pharmacy,
                'ndc_number' => $request->ndc_number,
                'indications' => $request->indications,
                'contraindications' => $request->contraindications,
                'side_effects' => $request->side_effects,
                'drug_interactions' => $request->drug_interactions,
                'storage_instructions' => $request->storage_instructions,
                'notes' => $request->notes,
                'is_active' => $request->is_active ?? true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Medication created successfully',
                'data' => new MedicationResource($medication)
            ], 201);
        } catch (\Exception $e) {
            \Log::error('Failed to create medication:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::user()?->id ?? 'unknown'
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create medication',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Display the specified medication.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $user = Auth::user();
            
            $medication = Medication::forUser($user->id)->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => new MedicationResource($medication)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Medication not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified medication.
     */
    public function update(UpdateMedicationRequest $request, string $id): JsonResponse
    {
        try {
            $user = Auth::user();
            \Log::info('Updating medication', ['user_id' => $user->id, 'medication_id' => $id, 'data' => $request->all()]);
            
            // 基本的な検証のみ
            if (!is_numeric($id) || (is_numeric($id) && intval($id) <= 0)) {
                return response()->json([
                    'success' => false,
                    'message' => '無効なIDです。',
                    'error' => 'Invalid ID'
                ], 400);
            }
            
            $medication = Medication::forUser($user->id)->findOrFail($id);
            \Log::info('Medication found for update', ['medication' => $medication->toArray()]);
            
            $validatedData = $request->validated();
            \Log::info('Validated data for update', ['validated_data' => $validatedData]);
            
            // 安全な更新のため、既知のフィールドのみを使用
            $allowedFields = [
                'name', 'description', 'image_path', 'generic_name', 'dosage_form', 
                'strength', 'manufacturer', 'prescription_number', 'prescribing_doctor', 
                'pharmacy', 'ndc_number', 'indications', 'contraindications', 
                'side_effects', 'drug_interactions', 'storage_instructions', 
                'notes', 'is_active', 'schedule'
            ];
            
            $updateData = collect($validatedData)
                ->only($allowedFields)
                ->filter(function ($value, $key) {
                    return $value !== null || in_array($key, ['description', 'notes', 'storage_instructions']);
                })
                ->toArray();
                
            \Log::info('Final update data', ['update_data' => $updateData]);
            
            $medication->update($updateData);
            \Log::info('Medication updated successfully');

            return response()->json([
                'success' => true,
                'message' => 'Medication updated successfully',
                'data' => new MedicationResource($medication->fresh())
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to update medication', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::user()?->id ?? 'unknown',
                'medication_id' => $id,
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update medication',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Remove the specified medication.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $user = Auth::user();
            
            // 基本的な検証のみ
            if (!is_numeric($id) || (is_numeric($id) && intval($id) <= 0)) {
                return response()->json([
                    'success' => false,
                    'message' => '無効なIDです。',
                    'error' => 'Invalid ID'
                ], 400);
            }
            
            $medication = Medication::forUser($user->id)->findOrFail($id);
            $medication->delete();

            return response()->json([
                'success' => true,
                'message' => 'Medication deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete medication',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
