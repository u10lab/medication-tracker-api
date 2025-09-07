<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class MedicationPatternController extends Controller
{
    /**
     * Get mock medication patterns data for testing
     */
    private function getMockPatterns($medicationId = null)
    {
        $allPatterns = [
            [
                'id' => 1,
                'medication_id' => 1,
                'pattern_type' => 'daily',
                'daily_doses' => 1,
                'doses_per_intake' => 1,
                'cycle_days_on' => null,
                'cycle_days_off' => null,
                'total_cycles' => null,
                'start_date' => '2024-01-15',
                'end_date' => '2024-12-31',
                'is_active' => true,
                'schedule_times' => ['08:00'],
                'created_at' => '2024-01-15T10:00:00Z',
                'updated_at' => '2024-01-15T10:00:00Z'
            ],
            [
                'id' => 2,
                'medication_id' => 2,
                'pattern_type' => 'as_needed',
                'daily_doses' => 3,
                'doses_per_intake' => 1,
                'cycle_days_on' => null,
                'cycle_days_off' => null,
                'total_cycles' => null,
                'start_date' => '2024-02-01',
                'end_date' => '2024-02-07',
                'is_active' => true,
                'schedule_times' => ['08:00', '14:00', '20:00'],
                'created_at' => '2024-02-01T14:30:00Z',
                'updated_at' => '2024-02-01T14:30:00Z'
            ]
        ];

        if ($medicationId) {
            return array_filter($allPatterns, fn($pattern) => $pattern['medication_id'] == $medicationId);
        }

        return $allPatterns;
    }

    /**
     * Display a listing of patterns for a specific medication.
     */
    public function index($medicationId): JsonResponse
    {
        try {
            $user = Auth::user();
            $patterns = $this->getMockPatterns($medicationId);

            return response()->json([
                'success' => true,
                'data' => array_values($patterns)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve patterns',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created pattern.
     */
    public function store(Request $request, $medicationId): JsonResponse
    {
        try {
            $user = Auth::user();
            
            $newPattern = array_merge($request->all(), [
                'id' => rand(100, 999),
                'medication_id' => (int)$medicationId,
                'is_active' => true,
                'created_at' => now()->toISOString(),
                'updated_at' => now()->toISOString()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pattern created successfully',
                'data' => $newPattern
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create pattern',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified pattern.
     */
    public function show($medicationId, string $id): JsonResponse
    {
        try {
            $user = Auth::user();
            $patterns = collect($this->getMockPatterns($medicationId));
            $pattern = $patterns->firstWhere('id', (int)$id);

            if (!$pattern) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pattern not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $pattern
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve pattern',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified pattern.
     */
    public function update(Request $request, $medicationId, string $id): JsonResponse
    {
        try {
            $user = Auth::user();
            $patterns = collect($this->getMockPatterns($medicationId));
            $pattern = $patterns->firstWhere('id', (int)$id);

            if (!$pattern) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pattern not found'
                ], 404);
            }

            $updatedPattern = array_merge($pattern, $request->all(), [
                'updated_at' => now()->toISOString()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pattern updated successfully',
                'data' => $updatedPattern
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update pattern',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified pattern.
     */
    public function destroy($medicationId, string $id): JsonResponse
    {
        try {
            $user = Auth::user();
            $patterns = collect($this->getMockPatterns($medicationId));
            $pattern = $patterns->firstWhere('id', (int)$id);

            if (!$pattern) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pattern not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Pattern deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete pattern',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
