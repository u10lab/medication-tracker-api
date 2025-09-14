<?php

use App\Http\Controllers\Api\MedicationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// 認証関連のルート（CSRF検証を除外）
Route::prefix('auth')->group(function () {
    Route::post('/token', function (Request $request) {
        \Log::info('Auth token request received', [
            'request_data' => $request->all(),
            'headers' => $request->headers->all()
        ]);

        $request->validate([
            'supabase_token' => 'required|string',
        ]);

        try {
            $supabaseAuth = app(\App\Services\SupabaseAuthService::class);
            
            // Verify Supabase token
            $supabaseUser = $supabaseAuth->verifyToken($request->supabase_token);
            
            if (!$supabaseUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Supabase token'
                ], 401);
            }

            // Get or create Laravel user
            $user = $supabaseAuth->getOrCreateUser($supabaseUser);
            
            // Create Sanctum token
            $token = $supabaseAuth->createSanctumToken($user);

            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'name' => $user->name,
                ],
                'token' => $token,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication failed',
                'error' => $e->getMessage()
            ], 500);
        }
    });

    Route::post('/revoke', function (Request $request) {
        try {
            $user = $request->user();
            $user->currentAccessToken()->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Token revoked successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to revoke token',
                'error' => $e->getMessage()
            ], 500);
        }
    })->middleware('auth:sanctum');
});

// 副作用タイプ取得（全ユーザー共通、認証不要）
Route::get('side-effect-types', function () {
    $sideEffects = \App\Models\SideEffectType::all();
    return response()->json([
        'success' => true,
        'data' => $sideEffects
    ]);
});

// 保護されたAPIルート（認証が必要）
Route::middleware('auth:sanctum')->group(function () {
    // 処方薬管理API
    Route::apiResource('medications', MedicationController::class);
    
    
    // 服薬ログ管理API
    Route::apiResource('medication-logs', \App\Http\Controllers\Api\MedicationLogController::class);
});

// テスト用メール送信エンドポイント（開発環境のみ）
    // 副作用タイプ
    Route::get('/side-effect-types', [\App\Http\Controllers\Api\SideEffectTypeController::class, 'index']);
    Route::get('/side-effect-types/category/{category}', [\App\Http\Controllers\Api\SideEffectTypeController::class, 'byCategory']);

Route::get('/test-email', function () {
    try {
        \Illuminate\Support\Facades\Mail::raw('🏥 処方薬管理アプリのテストメールです！

このメールが届いていれば、メール送信機能は正常に動作しています。

📧 送信日時: ' . now()->format('Y年m月d日 H:i:s') . '
🚀 送信元: 処方薬管理アプリ (Laravel API)

テスト完了！', function ($message) {
            $message->to('test@medication-tracker.app')
                    ->subject('🧪 処方薬管理アプリ - メール送信テスト');
        });

        return response()->json([
            'success' => true,
            'message' => 'テストメールを送信しました！Mailtrapで確認してください。'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'メール送信に失敗しました',
            'error' => $e->getMessage()
        ], 500);
    }
});