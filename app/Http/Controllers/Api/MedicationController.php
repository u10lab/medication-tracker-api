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
     * Get mock medications data for testing
     */
    private function getMockMedications()
    {
        return [
            [
                'id' => 1,
                'name' => 'アムロジピン',
                'generic_name' => 'Amlodipine',
                'description' => '高血圧・狭心症の治療薬（カルシウム拮抗薬）',
                'image' => 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjQiIGhlaWdodD0iNjQiIHZpZXdCb3g9IjAgMCA2NCA2NCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjY0IiBoZWlnaHQ9IjY0IiByeD0iOCIgZmlsbD0iIzE5NzZkMiIvPgo8dGV4dCB4PSIzMiIgeT0iNDAiIGZvbnQtZmFtaWx5PSJzYW5zLXNlcmlmIiBmb250LXNpemU9IjI0IiBmaWxsPSJ3aGl0ZSIgdGV4dC1hbmNob3I9Im1pZGRsZSI+💊PC90ZXh0Pgo8L3N2Zz4=',
                'dosage' => '5mg',
                'frequency' => '1日1回',
                'duration_days' => 30,
                'remaining_count' => 25,
                'total_count' => 30,
                'expiry_date' => '2024-12-31',
                'notes' => '朝食後に服用',
                'is_active' => true,
                'schedule' => [
                    'type' => 'daily',
                    'dosesPerDay' => 1,
                    'times' => ['08:00'],
                    'startDate' => '2024-01-15',
                    'endDate' => '2024-12-31'
                ],
                'created_at' => '2024-01-15T10:00:00Z',
                'updated_at' => '2024-01-15T10:00:00Z'
            ],
            [
                'id' => 2,
                'name' => 'ロキソニン',
                'generic_name' => 'Loxoprofen',
                'description' => '解熱鎮痛薬（NSAIDs）痛み・炎症を和らげる',
                'image' => 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjQiIGhlaWdodD0iNjQiIHZpZXdCb3g9IjAgMCA2NCA2NCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjY0IiBoZWlnaHQ9IjY0IiByeD0iOCIgZmlsbD0iI2Y1N2MwMCIvPgo8dGV4dCB4PSIzMiIgeT0iNDAiIGZvbnQtZmFtaWx5PSJzYW5zLXNlcmlmIiBmb250LXNpemU9IjI0IiBmaWxsPSJ3aGl0ZSIgdGV4dC1hbmNob3I9Im1pZGRsZSI+💊PC90ZXh0Pgo8L3N2Zz4=',
                'dosage' => '60mg',
                'frequency' => '1日3回',
                'duration_days' => 7,
                'remaining_count' => 18,
                'total_count' => 21,
                'expiry_date' => '2025-06-30',
                'notes' => '痛みがある時のみ服用',
                'is_active' => true,
                'schedule' => [
                    'type' => 'daily',
                    'dosesPerDay' => 3,
                    'times' => ['08:00', '12:00', '18:00'],
                    'startDate' => '2024-02-01',
                    'endDate' => '2025-06-30'
                ],
                'created_at' => '2024-02-01T14:30:00Z',
                'updated_at' => '2024-02-01T14:30:00Z'
            ]
        ];
    }

    /**
     * Display a listing of the user's medications.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            
            // Get mock data
            $medications = collect($this->getMockMedications());

            // Filter by active status
            if ($request->has('active')) {
                $active = filter_var($request->get('active'), FILTER_VALIDATE_BOOLEAN);
                $medications = $medications->filter(function ($medication) use ($active) {
                    return $medication['is_active'] === $active;
                });
            }

            // Search by name
            if ($request->has('search')) {
                $search = $request->get('search');
                $medications = $medications->filter(function ($medication) use ($search) {
                    return stripos($medication['name'], $search) !== false ||
                           stripos($medication['generic_name'], $search) !== false;
                });
            }

            // Convert to array for pagination
            $medicationsArray = $medications->values()->all();
            $perPage = $request->get('per_page', 15);
            $currentPage = $request->get('page', 1);
            $offset = ($currentPage - 1) * $perPage;
            $itemsForCurrentPage = array_slice($medicationsArray, $offset, $perPage);

            // Create paginated response
            $paginated = new LengthAwarePaginator(
                $itemsForCurrentPage,
                count($medicationsArray),
                $perPage,
                $currentPage,
                ['path' => $request->url(), 'query' => $request->query()]
            );

            return response()->json([
                'success' => true,
                'data' => $paginated->items(),
                'meta' => [
                    'current_page' => $paginated->currentPage(),
                    'last_page' => $paginated->lastPage(),
                    'per_page' => $paginated->perPage(),
                    'total' => $paginated->total()
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
    public function store(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            
            // Mock create operation - return new medication with ID
            $newMedication = array_merge($request->all(), [
                'id' => rand(100, 999),
                'user_id' => $user->id,
                'is_active' => true,
                'created_at' => now()->toISOString(),
                'updated_at' => now()->toISOString()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Medication created successfully',
                'data' => $newMedication
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
            
            // Find medication in mock data
            $medications = collect($this->getMockMedications());
            $medication = $medications->firstWhere('id', (int)$id);

            if (!$medication) {
                return response()->json([
                    'success' => false,
                    'message' => 'Medication not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $medication
            ]);
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
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $user = Auth::user();
            
            // Find medication in mock data
            $medications = collect($this->getMockMedications());
            $medication = $medications->firstWhere('id', (int)$id);

            if (!$medication) {
                return response()->json([
                    'success' => false,
                    'message' => 'Medication not found'
                ], 404);
            }

            // Mock update - merge request data
            $updatedMedication = array_merge($medication, $request->all(), [
                'updated_at' => now()->toISOString()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Medication updated successfully',
                'data' => $updatedMedication
            ]);
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
            
            // Find medication in mock data
            $medications = collect($this->getMockMedications());
            $medication = $medications->firstWhere('id', (int)$id);

            if (!$medication) {
                return response()->json([
                    'success' => false,
                    'message' => 'Medication not found'
                ], 404);
            }

            // Mock delete - always successful
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
