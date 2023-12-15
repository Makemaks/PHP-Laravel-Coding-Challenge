<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Project;
use App\Models\User;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanCreateProject()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create();
        $this->actingAs($user);

        $projectData = [
            'name' => 'Example Project',
            'description' => 'This is a test project.',
            'company_id' => $company->id,
        ];

        $response = $this->postJson('/api/projects', $projectData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'project' => [
                    'id',
                    'name',
                    'description',
                    'company_id',
                    'user_id',
                    'created_at',
                    'updated_at',
                ],
            ])
            ->assertJson([
                'message' => 'Project created successfully',
                'project' => $projectData,
            ]);

        $this->assertDatabaseHas('projects', $projectData);
    }

    public function testUserCanViewProject()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $project = Project::factory()->create();

        $response = Cache::remember("project_{$project->id}_response", now()->addMinutes(10), function () use ($project) {
            return $this->getJson("/api/projects/{$project->id}");
        });

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $project->id,
                'name' => $project->name,
                'description' => $project->description,
                'company_id' => $project->company_id,
                'user_id' => $project->user_id,
            ]);
    }

    public function testUserCanUpdateProject()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $project = Project::factory()->create();

        $updatedData = [
            'name' => 'Updated Project Name',
            'description' => 'Updated project description.',
            'company_id' => $project->company_id,
        ];

        $response = $this->putJson("/api/projects/{$project->id}", $updatedData);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Project updated successfully',
                'project' => $updatedData,
            ]);

        $this->assertDatabaseHas('projects', $updatedData);
    }

    public function testUserCanDeleteProject()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $project = Project::factory()->create();

        $response = $this->deleteJson("/api/projects/{$project->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
    }
}
