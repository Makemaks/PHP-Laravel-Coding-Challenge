<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

class CompanyTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanCreateCompany()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $companyData = [
            'name' => 'Example Company',
            'description' => 'This is a test company.',
            'address' => '123 Test Street',
            'email' => 'company@example.com',
        ];

        $response = $this->postJson('/api/company', $companyData);

        $response->assertStatus(201)
            ->assertJson($companyData);

        $this->assertDatabaseHas('companies', $companyData);
    }

    public function testUserCanViewCompany()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $company = Company::factory()->create();

        $response = Cache::remember("company_{$company->id}_response", now()->addMinutes(10), function () use ($company) {
            return $this->getJson("/api/company/{$company->id}");
        });

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => $company->name]);
    }

    public function testUserCanUpdateCompany()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $company = Company::factory()->create();

        $updatedData = [
            'name' => 'Updated Company Name',
            'description' => 'Updated company description.',
            'address' => '456 Updated Street',
            'email' => 'updated@example.com',
        ];

        $response = $this->putJson("/api/company/{$company->id}", $updatedData);

        $response->assertStatus(200)
            ->assertJson(['id' => $company->id, 'name' => 'Updated Company Name', 'description' => 'Updated company description.', 'address' => '456 Updated Street', 'email' => 'updated@example.com']);

        $this->assertDatabaseHas('companies', $updatedData);
    }

    public function testUserCanDeleteCompany()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $company = Company::factory()->create();

        $response = $this->deleteJson("/api/company/{$company->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('companies', ['id' => $company->id]);
    }
}