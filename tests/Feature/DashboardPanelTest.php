<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class DashboardPanelTest extends TestCase
{
    public function test_personal_panels_render_for_regular_users(): void
    {
        $this->actingAs($this->user('user'));

        $html = Blade::render(<<<'BLADE'
            <x-dashboard.panel title="My library">
                <p>18 tracked</p>
            </x-dashboard.panel>
        BLADE);

        $this->assertStringContainsString('My library', $html);
        $this->assertStringContainsString('18 tracked', $html);
        $this->assertStringContainsString('bg-gray-800', $html);
    }

    public function test_superuser_panels_render_nothing_for_regular_users(): void
    {
        $this->actingAs($this->user('user'));

        $html = Blade::render(<<<'BLADE'
            <x-dashboard.panel title="Incomplete scans" :superuser="true">
                <p>admin only</p>
            </x-dashboard.panel>
        BLADE);

        $this->assertSame('', trim($html));
    }

    public function test_superuser_panels_render_for_admins(): void
    {
        $this->actingAs($this->user('admin'));

        $html = Blade::render(<<<'BLADE'
            <x-dashboard.panel title="Incomplete scans" :superuser="true">
                <p>2 incomplete</p>
            </x-dashboard.panel>
        BLADE);

        $this->assertStringContainsString('Incomplete scans', $html);
        $this->assertStringContainsString('2 incomplete', $html);
    }

    public function test_superuser_panels_render_nothing_for_guests(): void
    {
        $html = Blade::render(<<<'BLADE'
            <x-dashboard.panel title="Catalog" :superuser="true">
                <p>hidden</p>
            </x-dashboard.panel>
        BLADE);

        $this->assertSame('', trim($html));
    }

    private function user(string $role): User
    {
        $user = new User([
            'name' => 'Reader',
            'email' => 'reader@example.com',
            'role' => $role,
        ]);
        $user->id = 1;

        return $user;
    }
}
