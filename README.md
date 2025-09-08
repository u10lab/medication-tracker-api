# 処方薬管理アプリケーション（バックエンドAPI）

## 📋 プロジェクト概要

個人の処方薬服薬管理、副作用記録、服薬統計を効率的に行うWebアプリケーションのバックエンドAPI部分です。

### 主な機能
- ✅ RESTful API設計
- ✅ ユーザー認証（Laravel Sanctum + Supabase Auth）
- ✅ 処方薬管理（CRUD操作）
- ✅ 服薬記録管理
- ✅ 副作用記録管理
- ✅ データバリデーション・エラーハンドリング

## 🏗 技術スタック

- **フレームワーク**: Laravel 11
- **認証**: Laravel Sanctum + Supabase Auth（ハイブリッド）
- **データベース**: Supabase PostgreSQL
- **ORM**: Eloquent
- **バリデーション**: Laravel Form Requests
- **レスポンス**: Laravel API Resources

## 🚀 セットアップ

### 前提条件
- PHP 8.2以上
- Composer
- Supabaseアカウント

### インストール

```bash
# 依存関係のインストール
composer install

# 環境変数の設定
cp .env.example .env
```

### 環境変数設定

`.env`ファイルに以下を設定：

```env
# アプリケーション設定
APP_NAME="Medication Tracker API"
APP_ENV=local
APP_KEY=base64:your_app_key
APP_DEBUG=true
APP_URL=http://localhost:8000

# データベース設定（Supabase PostgreSQL）
DB_CONNECTION=pgsql
DB_HOST=your_supabase_host
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=your_supabase_password
DB_SSLMODE=require

# Supabase設定
SUPABASE_URL=your_supabase_url
SUPABASE_ANON_KEY=your_supabase_anon_key
SUPABASE_SERVICE_ROLE_KEY=your_supabase_service_role_key

# Sanctum設定
SANCTUM_STATEFUL_DOMAINS=localhost:5173
SESSION_DRIVER=database
SESSION_DOMAIN=localhost
```

### データベース設定

```bash
# アプリケーションキー生成
php artisan key:generate

# データベースマイグレーション実行
php artisan migrate

# シードデータ投入
php artisan db:seed

# Sanctumマイグレーション実行
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

### 開発サーバー起動

```bash
# 開発サーバー起動
php artisan serve

# APIは http://localhost:8000/api でアクセス可能
```

## 📁 プロジェクト構成

```
app/
├── Http/Controllers/Api/    # APIコントローラー
│   ├── AuthController.php
│   ├── MedicationController.php
│   ├── MedicationLogController.php
│   └── SideEffectTypeController.php
├── Models/                  # Eloquentモデル
│   ├── Medication.php
│   ├── MedicationLog.php
│   └── SideEffectType.php
├── Http/Requests/          # バリデーション
│   ├── StoreMedicationRequest.php
│   └── UpdateMedicationRequest.php
└── Services/               # ビジネスロジック
    └── SupabaseAuthService.php

database/
├── migrations/             # DBマイグレーション
│   ├── 2024_01_01_000001_create_medications_table.php
│   ├── 2024_01_01_000003_create_medication_logs_table.php
│   └── 2024_01_01_000004_create_side_effect_types_table.php
└── seeders/               # シードデータ
    ├── DatabaseSeeder.php
    └── SideEffectTypeSeeder.php

routes/
└── api.php                # APIルート定義
```

## 🔌 API エンドポイント

### 認証
```
POST   /api/auth/token      # トークン取得
POST   /api/auth/logout     # ログアウト
```

### 処方薬管理
```
GET    /api/medications           # 処方薬一覧取得
POST   /api/medications           # 処方薬作成
GET    /api/medications/{id}      # 処方薬詳細取得
PUT    /api/medications/{id}      # 処方薬更新
DELETE /api/medications/{id}      # 処方薬削除
```

### 服薬記録
```
GET    /api/medication-logs       # 服薬記録一覧取得
POST   /api/medication-logs       # 服薬記録作成
GET    /api/medication-logs/{id}  # 服薬記録詳細取得
PUT    /api/medication-logs/{id}  # 服薬記録更新
DELETE /api/medication-logs/{id}  # 服薬記録削除
```

### 副作用タイプ
```
GET    /api/side-effect-types     # 副作用タイプ一覧取得
```

## 🔐 認証システム

### ハイブリッド認証
1. **フロントエンド**: Supabase Authでユーザー認証
2. **バックエンド**: Laravel SanctumでAPI認証
3. **トークン検証**: Supabase JWTトークンをLaravelで検証

### 認証フロー
```php
// 1. Supabase JWTトークン検証
$user = SupabaseAuthService::verifyToken($request->bearerToken());

// 2. Laravel Sanctumトークン発行
$token = $user->createToken('api-token');

// 3. API認証
Auth::guard('sanctum')->user();
```

## 🗄 データベース設計

### 主要テーブル

#### medications
```sql
- id (BIGSERIAL PRIMARY KEY)
- user_id (BIGINT)
- name (VARCHAR)
- description (TEXT)
- schedule (JSONB)  -- スケジュール情報
- created_at, updated_at
```

#### medication_logs
```sql
- id (BIGSERIAL PRIMARY KEY)
- medication_id (BIGINT)
- scheduled_date (DATE)
- scheduled_time (TIME)
- actual_time (TIMESTAMP)
- status (VARCHAR)  -- taken, missed, skipped
- side_effects (TEXT[])
- notes (TEXT)
- created_at, updated_at
```

#### side_effect_types
```sql
- id (BIGSERIAL PRIMARY KEY)
- name (VARCHAR UNIQUE)
- category (VARCHAR)
- description (TEXT)
- created_at, updated_at
```

## 🔧 開発コマンド

```bash
# 開発サーバー起動
php artisan serve

# マイグレーション実行
php artisan migrate

# シードデータ投入
php artisan db:seed

# キャッシュクリア
php artisan cache:clear
php artisan config:clear

# ルート一覧表示
php artisan route:list

# テスト実行
php artisan test
```

## 🚀 デプロイ

### Railway（推奨）

```bash
# Railway CLIでデプロイ
npm install -g @railway/cli
railway login
railway init
railway up
```

### その他のプラットフォーム

- **Heroku**: `composer.json`でPHP設定
- **DigitalOcean App Platform**: Laravel対応
- **AWS Elastic Beanstalk**: PHP環境

### 環境変数設定（本番）

本番環境では以下の環境変数を設定：

```env
APP_ENV=production
APP_DEBUG=false
DB_HOST=your_production_supabase_host
SUPABASE_URL=your_production_supabase_url
SANCTUM_STATEFUL_DOMAINS=your_production_domain
```

## 🧪 テスト

```bash
# 全テスト実行
php artisan test

# 特定のテスト実行
php artisan test --filter=MedicationTest

# カバレッジ付きテスト
php artisan test --coverage
```

## 📊 ログ・監視

### ログ設定
```php
// config/logging.php
'channels' => [
    'daily' => [
        'driver' => 'daily',
        'path' => storage_path('logs/laravel.log'),
        'level' => 'debug',
        'days' => 14,
    ],
],
```

### エラーハンドリング
- グローバル例外ハンドラー
- API エラーレスポンス統一
- バリデーションエラー詳細表示

## 🐛 トラブルシューティング

### よくある問題

1. **Supabase接続エラー**
   - SSL設定確認（`DB_SSLMODE=require`）
   - 接続情報の確認
   - ファイアウォール設定確認

2. **認証エラー**
   - Sanctum設定確認
   - CORS設定確認
   - トークン有効期限確認

3. **マイグレーションエラー**
   - データベース接続確認
   - マイグレーションファイル確認
   - 権限設定確認

## 📚 開発情報

- **開発期間**: 2025年9月4日〜9月8日（5日間）
- **開発者**: Claude + cursor + Human（ペアプログラミング）
- **開発スタイル**: アジャイル・反復開発
- **完成状況**: MVP完成、デプロイ準備完了

## 📄 ライセンス

このプロジェクトはMITライセンスの下で公開されています。

## 🤝 コントリビューション

1. このリポジトリをフォーク
2. フィーチャーブランチを作成 (`git checkout -b feature/amazing-feature`)
3. 変更をコミット (`git commit -m 'Add some amazing feature'`)
4. ブランチにプッシュ (`git push origin feature/amazing-feature`)
5. プルリクエストを作成

---

**詳細な要件定義は [../medication-tracker/requirement.md](../medication-tracker/requirement.md) を参照してください。**
