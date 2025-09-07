<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class MedicationLogController extends Controller
{
    /**
     * Get mock medication logs data for testing
     */
    private function getMockLogs()
    {
        return [
            [
                'id' => 1,
                'medication_pattern_id' => 1,
                'scheduled_date' => '2024-09-07',
                'scheduled_time' => '08:00:00',
                'actual_time' => '2024-09-07 08:15:00',
                'status' => 'taken',
                'side_effects' => ['眠気'],
                'notes' => '少し眠気を感じた',
                'severity_level' => 'mild',
                'created_at' => '2024-09-07T08:15:00Z',
                'updated_at' => '2024-09-07T08:15:00Z'
            ],
            [
                'id' => 2,
                'medication_pattern_id' => 1,
                'scheduled_date' => '2024-09-06',
                'scheduled_time' => '08:00:00',
                'actual_time' => '2024-09-06 08:00:00',
                'status' => 'taken',
                'side_effects' => null,
                'notes' => '問題なし',
                'severity_level' => null,
                'created_at' => '2024-09-06T08:00:00Z',
                'updated_at' => '2024-09-06T08:00:00Z'
            ],
            [
                'id' => 3,
                'medication_pattern_id' => 2,
                'scheduled_date' => '2024-09-05',
                'scheduled_time' => '14:00:00',
                'actual_time' => null,
                'status' => 'missed',
                'side_effects' => null,
                'notes' => '飲み忘れ',
                'severity_level' => null,
                'created_at' => '2024-09-05T14:00:00Z',
                'updated_at' => '2024-09-05T14:00:00Z'
            ]
        ];
    }

    /**
     * Display a listing of medication logs.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $logs = collect($this->getMockLogs());

            // Filter by date range if provided
            if ($request->has('start_date') && $request->has('end_date')) {
                $startDate = $request->get('start_date');
                $endDate = $request->get('end_date');
                $logs = $logs->filter(function ($log) use ($startDate, $endDate) {
                    return $log['scheduled_date'] >= $startDate && $log['scheduled_date'] <= $endDate;
                });
            }

            // Filter by status if provided
            if ($request->has('status')) {
                $status = $request->get('status');
                $logs = $logs->filter(function ($log) use ($status) {
                    return $log['status'] === $status;
                });
            }

            return response()->json([
                'success' => true,
                'data' => $logs->values()->all()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve logs',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created medication log.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            
            $newLog = array_merge($request->all(), [
                'id' => rand(100, 999),
                'created_at' => now()->toISOString(),
                'updated_at' => now()->toISOString()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Log created successfully',
                'data' => $newLog
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create log',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified medication log.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $user = Auth::user();
            $logs = collect($this->getMockLogs());
            $log = $logs->firstWhere('id', (int)$id);

            if (!$log) {
                return response()->json([
                    'success' => false,
                    'message' => 'Log not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $log
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve log',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified medication log.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $user = Auth::user();
            $logs = collect($this->getMockLogs());
            $log = $logs->firstWhere('id', (int)$id);

            if (!$log) {
                return response()->json([
                    'success' => false,
                    'message' => 'Log not found'
                ], 404);
            }

            $updatedLog = array_merge($log, $request->all(), [
                'updated_at' => now()->toISOString()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Log updated successfully',
                'data' => $updatedLog
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update log',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified medication log.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $user = Auth::user();
            $logs = collect($this->getMockLogs());
            $log = $logs->firstWhere('id', (int)$id);

            if (!$log) {
                return response()->json([
                    'success' => false,
                    'message' => 'Log not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Log deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete log',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
