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
        $sideEffects = [
            ['id' => 1, 'name' => '頭痛', 'category' => '神経系'],
            ['id' => 2, 'name' => '吐き気', 'category' => '消化器系'],
            ['id' => 3, 'name' => 'めまい', 'category' => '神経系'],
            ['id' => 4, 'name' => '疲労感', 'category' => '全身症状'],
            ['id' => 5, 'name' => '発疹', 'category' => '皮膚症状'],
            ['id' => 6, 'name' => '下痢', 'category' => '消化器系'],
            ['id' => 7, 'name' => '便秘', 'category' => '消化器系'],
            ['id' => 8, 'name' => '食欲不振', 'category' => '消化器系'],
            ['id' => 9, 'name' => '不眠', 'category' => '神経系'],
            ['id' => 10, 'name' => '眠気', 'category' => '神経系']
        ];
        return response()->json([
            'success' => true,
            'data' => $sideEffects
        ]);
    });
});

// テスト用メール送信エンドポイント（開発環境のみ）
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