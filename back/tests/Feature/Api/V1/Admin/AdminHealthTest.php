<?php

namespace Tests\Feature\Api\V1\Admin;

use Tests\TestCase;

class AdminHealthTest extends TestCase
{
    public function test_admin_health_endpoint_returns_success(): void
    {
        $response = $this->getJson('/api/v1/admin/health');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.service', 'ayt-backend-admin')
            ->assertJsonPath('data.version', 'v1')
            ->assertJsonStructure([
                'data' => ['service', 'version', 'timestamp', 'request_id'],
            ]);
    }
}
