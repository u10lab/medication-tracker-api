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

class MedicationController extends Controller
{
    /**
     * Display a listing of the user's medications.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            
            $query = Medication::forUser($user->id)
                ->with(['medicationPatterns', 'medicationLogs']);

            // Filter by active status
            if ($request->has('active')) {
                $active = filter_var($request->get('active'), FILTER_VALIDATE_BOOLEAN);
                if ($active) {
                    $query->active();
                } else {
                    $query->where('is_active', false);
                }
            }

            // Search by name
            if ($request->has('search')) {
                $search = $request->get('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'ILIKE', "%{$search}%")
                      ->orWhere('generic_name', 'ILIKE', "%{$search}%");
                });
            }

            // Pagination
            $perPage = $request->get('per_page', 15);
            $medications = $query->latest()->paginate($perPage);

            return new MedicationCollection($medications);
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
            
            $medication = Medication::create(array_merge(
                $request->validated(),
                ['user_id' => $user->id]
            ));

            $medication->load(['medicationPatterns', 'medicationLogs']);

            return response()->json([
                'success' => true,
                'message' => 'Medication created successfully',
                'data' => new MedicationResource($medication)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create medication',
                'error' => $e->getMessage()
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
            
            $medication = Medication::forUser($user->id)
                ->with(['medicationPatterns', 'medicationLogs'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => new MedicationResource($medication)
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Medication not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve medication',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified medication.
     */
    public function update(UpdateMedicationRequest $request, string $id): JsonResponse
    {
        try {
            $user = Auth::user();
            
            $medication = Medication::forUser($user->id)->findOrFail($id);

            $medication->update($request->validated());
            $medication->load(['medicationPatterns', 'medicationLogs']);

            return response()->json([
                'success' => true,
                'message' => 'Medication updated successfully',
                'data' => new MedicationResource($medication)
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Medication not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update medication',
                'error' => $e->getMessage()
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
            
            $medication = Medication::forUser($user->id)->findOrFail($id);
            $medication->delete();

            return response()->json([
                'success' => true,
                'message' => 'Medication deleted successfully'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Medication not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete medication',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
