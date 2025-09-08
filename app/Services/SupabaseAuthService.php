<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SupabaseAuthService
{
    private ?string $supabaseUrl;
    private ?string $supabaseServiceKey;

    public function __construct()
    {
        $this->supabaseUrl = config('services.supabase.url');
        $this->supabaseServiceKey = config('services.supabase.service_key');
    }

    /**
     * Verify Supabase JWT token and get user info
     */
    public function verifyToken(string $token): ?array
    {
        try {
            Log::info('Verifying Supabase token', ['token_length' => strlen($token)]);
            
            // 開発環境でのテスト用ダミー実装
            if (config('app.env') === 'local') {
                // Supabase JWT トークンをデコードしてユーザー情報を取得
                if (str_starts_with($token, 'eyJ')) {
                    try {
                        $payload = json_decode(base64_decode(str_replace('_', '/', str_replace('-', '+', explode('.', $token)[1]))), true);
                        Log::info('Using JWT payload for local development', ['payload' => $payload]);
                        
                        return [
                            'id' => $payload['sub'] ?? 'test-user-id',
                            'email' => $payload['email'] ?? 'test@example.com',
                            'user_metadata' => [
                                'name' => $payload['user_metadata']['name'] ?? 'Test User'
                            ],
                            'email_confirmed_at' => \Carbon\Carbon::now()->toISOString()
                        ];
                    } catch (\Exception $e) {
                        Log::warning('Failed to decode JWT, using fallback', ['error' => $e->getMessage()]);
                    }
                }
                
                if ($token === 'test_token') {
                    Log::info('Using test token for local development');
                    return [
                        'id' => 'test-user-id',
                        'email' => 'test@example.com',
                        'user_metadata' => [
                            'name' => 'Test User'
                        ],
                        'email_confirmed_at' => \Carbon\Carbon::now()->toISOString()
                    ];
                }
            }

            if (!$this->supabaseUrl || !$this->supabaseServiceKey) {
                Log::warning('Supabase configuration missing', [
                    'url' => $this->supabaseUrl ? 'set' : 'missing',
                    'service_key' => $this->supabaseServiceKey ? 'set' : 'missing'
                ]);
                return null;
            }

            Log::info('Making request to Supabase', ['url' => $this->supabaseUrl]);
            
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$token}",
                'apikey' => $this->supabaseServiceKey,
            ])->get("{$this->supabaseUrl}/auth/v1/user");

            Log::info('Supabase response', [
                'status' => $response->status(),
                'successful' => $response->successful()
            ]);

            if ($response->successful()) {
                $userData = $response->json();
                Log::info('Supabase user verified', ['user_id' => $userData['id'] ?? 'unknown']);
                return $userData;
            }

            Log::warning('Supabase token verification failed', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Supabase token verification error', [
                'error' => $e->getMessage()
            ]);

            return null;
        }
    }

    /**
     * Get or create Laravel user from Supabase user data
     */
    public function getOrCreateUser(array $supabaseUser): User
    {
        $email = $supabaseUser['email'] ?? null;
        $supabaseId = $supabaseUser['id'] ?? null;

        if (!$email || !$supabaseId) {
            throw new \InvalidArgumentException('Invalid Supabase user data');
        }

        // Try to find existing user by email
        $user = User::where('email', $email)->first();

        if (!$user) {
            // Create new user
            $user = User::create([
                'name' => $supabaseUser['user_metadata']['name'] ?? $email,
                'email' => $email,
                'password' => bcrypt(Str::random(32)), // Random password since we use Supabase auth
                'email_verified_at' => $supabaseUser['email_confirmed_at'] ? \Carbon\Carbon::now() : null,
            ]);
        }

        return $user;
    }

    /**
     * Create Sanctum token for user
     */
    public function createSanctumToken(User $user): string
    {
        return $user->createToken('api-token')->plainTextToken;
    }
}
