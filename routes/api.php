<?php

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
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('medications')->group(function () {
        Route::get('/', function () {
            return response()->json(['message' => 'Medications endpoint - Coming soon']);
        });
    });
});