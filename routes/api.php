<?php

use App\Http\Controllers\Api\MedicationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// 認証関連のルート
Route::prefix('auth')->group(function () {
    Route::post('/token', function (Request $request) {
        // 一時的にデータベースを使用しないバージョン
        $request->validate([
            'supabase_user_id' => 'required|string',
            'email' => 'required|email',
            'name' => 'nullable|string',
        ]);

        // 一時的なダミートークンを返す（データベース未設定のため）
        $dummyToken = 'dummy_token_' . time() . '_' . $request->supabase_user_id;

        return response()->json([
            'user' => [
                'id' => 1,
                'email' => $request->email,
                'name' => $request->name ?? $request->email,
                'supabase_user_id' => $request->supabase_user_id,
            ],
            'token' => $dummyToken,
        ]);
    });

    Route::post('/revoke', function (Request $request) {
        // 一時的な実装
        return response()->json(['message' => 'Token revoked (dummy implementation)']);
    });
});

// 保護されたAPIルート（認証が必要）
Route::middleware(\App\Http\Middleware\SimpleDummyAuth::class)->group(function () {
    // 処方薬管理API
    Route::apiResource('medications', MedicationController::class);
    
    // 処方薬パターン管理API
    Route::prefix('medications/{medication}')->group(function () {
        Route::apiResource('patterns', \App\Http\Controllers\Api\MedicationPatternController::class);
    });
    
    // 服薬ログ管理API
    Route::apiResource('medication-logs', \App\Http\Controllers\Api\MedicationLogController::class);
    
    // 副作用タイプ取得（全ユーザー共通）
    Route::get('side-effect-types', function () {
        $sideEffects = \App\Models\SideEffectType::all();
        return response()->json([
            'success' => true,
            'data' => $sideEffects
        ]);
    });
});