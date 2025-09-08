<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MedicationLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class MedicationLogController extends Controller
{

    /**
     * Display a listing of medication logs.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            \Log::info('MedicationLog index called', ['user_id' => $user->id]);
            
            // Get user's medications directly
            $userMedicationIds = \App\Models\Medication::where('user_id', $user->id)->pluck('id');
            \Log::info('User medication IDs', ['ids' => $userMedicationIds->toArray()]);
            
            $query = MedicationLog::whereIn('medication_id', $userMedicationIds)
                ->with('medication');

            // Filter by date range if provided
            if ($request->has('start_date') && $request->has('end_date')) {
                $startDate = $request->get('start_date');
                $endDate = $request->get('end_date');
                $query->byDateRange($startDate, $endDate);
            }

            // Filter by status if provided
            if ($request->has('status')) {
                $status = $request->get('status');
                $query->byStatus($status);
            }

            $logs = $query->orderBy('scheduled_date', 'desc')
                ->orderBy('scheduled_time', 'desc')
                ->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $logs->items(),
                'meta' => [
                    'current_page' => $logs->currentPage(),
                    'last_page' => $logs->lastPage(),
                    'per_page' => $logs->perPage(),
                    'total' => $logs->total()
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to retrieve medication logs', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::user()?->id ?? 'unknown'
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve logs',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
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
            \Log::info('MedicationLog store called', ['user_id' => $user->id, 'request_data' => $request->all()]);
            
            $validatedData = $request->validate([
                'medication_id' => 'required|exists:medications,id',
                'scheduled_date' => 'required|date',
                'scheduled_time' => 'required|date_format:H:i:s',
                'status' => 'required|in:taken,missed,skipped',
                'actual_time' => 'nullable|date',
                'side_effects' => 'nullable|array',
                'notes' => 'nullable|string',
                'severity_level' => 'nullable|in:mild,moderate,severe'
            ]);

            // Verify the medication belongs to user
            $medication = \App\Models\Medication::where('user_id', $user->id)
                ->findOrFail($validatedData['medication_id']);

            \Log::info('Creating medication log', ['validated_data' => $validatedData]);
            $log = MedicationLog::create($validatedData);
            \Log::info('Medication log created successfully', ['log_id' => $log->id]);

            return response()->json([
                'success' => true,
                'message' => 'Log created successfully',
                'data' => $log->load('medication')
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
            
            // Get user's medication IDs first
            $userMedicationIds = \App\Models\Medication::where('user_id', $user->id)->pluck('id');
            
            $log = MedicationLog::whereIn('medication_id', $userMedicationIds)
                ->with('medication')
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $log
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Log not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified medication log.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $user = Auth::user();
            
            $request->validate([
                'scheduled_date' => 'sometimes|date',
                'scheduled_time' => 'sometimes|date_format:H:i:s',
                'status' => 'sometimes|in:taken,missed,skipped',
                'actual_time' => 'nullable|date',
                'side_effects' => 'nullable|array',
                'notes' => 'nullable|string',
                'severity_level' => 'nullable|in:mild,moderate,severe'
            ]);

            // Get user's medication IDs first
            $userMedicationIds = \App\Models\Medication::where('user_id', $user->id)->pluck('id');
            
            $log = MedicationLog::whereIn('medication_id', $userMedicationIds)
                ->findOrFail($id);
            
            $log->update($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Log updated successfully',
                'data' => $log->load('medication')
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
            \Log::info('MedicationLog destroy called', ['user_id' => $user->id, 'log_id' => $id]);
            
            // Get user's medication IDs first
            $userMedicationIds = \App\Models\Medication::where('user_id', $user->id)->pluck('id');
            \Log::info('User medication IDs', ['ids' => $userMedicationIds->toArray()]);
            
            $log = MedicationLog::whereIn('medication_id', $userMedicationIds)
                ->findOrFail($id);
            \Log::info('Log found for deletion', ['log' => $log->toArray()]);
            
            $log->delete();
            \Log::info('Log deleted successfully');

            return response()->json([
                'success' => true,
                'message' => 'Log deleted successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to delete medication log', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::user()?->id ?? 'unknown',
                'log_id' => $id
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete log',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}
