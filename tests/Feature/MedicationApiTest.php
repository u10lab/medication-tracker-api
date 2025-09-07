<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Medication;

class MedicationApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test user
        $this->user = User::factory()->create([
            'email' => 'test@example.com',
            'name' => 'Test User'
        ]);
    }

    public function test_can_get_medications_list(): void
    {
        // Create test medications
        $medications = Medication::factory()->count(3)->create([
            'user_id' => $this->user->id
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer dummy_token_' . time() . '_test_user_123',
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->get('/api/medications');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'description',
                    'created_at',
                    'updated_at'
                ]
            ]
        ]);
    }

    public function test_can_create_medication(): void
    {
        $medicationData = [
            'name' => 'Test Medication',
            'description' => 'A test medication for unit testing'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer dummy_token_' . time() . '_test_user_123',
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->post('/api/medications', $medicationData);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'id',
                'name',
                'description',
                'user_id',
                'created_at',
                'updated_at'
            ]
        ]);

        $this->assertDatabaseHas('medications', [
            'name' => 'Test Medication',
            'description' => 'A test medication for unit testing',
            'user_id' => $this->user->id
        ]);
    }

    public function test_can_get_single_medication(): void
    {
        $medication = Medication::factory()->create([
            'user_id' => $this->user->id
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer dummy_token_' . time() . '_test_user_123',
            'Accept' => 'application/json',
        ])->get("/api/medications/{$medication->id}");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'id',
                'name',
                'description',
                'user_id',
                'created_at',
                'updated_at'
            ]
        ]);
    }

    public function test_can_update_medication(): void
    {
        $medication = Medication::factory()->create([
            'user_id' => $this->user->id
        ]);

        $updateData = [
            'name' => 'Updated Medication Name',
            'description' => 'Updated description'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer dummy_token_' . time() . '_test_user_123',
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->put("/api/medications/{$medication->id}", $updateData);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'name' => 'Updated Medication Name',
            'description' => 'Updated description'
        ]);

        $this->assertDatabaseHas('medications', [
            'id' => $medication->id,
            'name' => 'Updated Medication Name',
            'description' => 'Updated description'
        ]);
    }

    public function test_can_delete_medication(): void
    {
        $medication = Medication::factory()->create([
            'user_id' => $this->user->id
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer dummy_token_' . time() . '_test_user_123',
            'Accept' => 'application/json',
        ])->delete("/api/medications/{$medication->id}");

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'success' => true,
            'message' => 'Medication deleted successfully'
        ]);

        $this->assertDatabaseMissing('medications', [
            'id' => $medication->id
        ]);
    }

    public function test_unauthorized_request_returns_401(): void
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->get('/api/medications');

        $response->assertStatus(401);
        $response->assertJsonFragment([
            'success' => false,
            'message' => 'Unauthorized'
        ]);
    }

    public function test_validation_errors_for_medication_creation(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer dummy_token_' . time() . '_test_user_123',
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->post('/api/medications', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name']);
    }
}
