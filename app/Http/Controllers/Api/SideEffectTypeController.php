<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class SideEffectTypeController extends Controller
{
    /**
     * Display a listing of side effect types.
     */
    public function index(): JsonResponse
    {
        try {
            $sideEffects = DB::table('side_effect_types')
                ->orderBy('category')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $sideEffects
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve side effect types',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get side effects by category.
     */
    public function byCategory(string $category): JsonResponse
    {
        try {
            $sideEffects = DB::table('side_effect_types')
                ->where('category', $category)
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $sideEffects
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve side effect types by category',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
