<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PageTest extends TestCase
{
    use RefreshDatabase;

    #[DataProvider('publicPages')]
    public function test_public_pages_render(string $uri): void
    {
        $this->get($uri)->assertOk();
    }

    public static function publicPages(): array
    {
        return [['/'], ['about'], ['services'], ['contact'], ['signal'], ['login'], ['register'], ['posts']];
    }

    public function test_projects_page_requires_login(): void
    {
        $this->get(route('projects'))->assertRedirect(route('login'));

        $this->actingAs(User::factory()->create())
            ->get(route('projects'))
            ->assertOk();
    }
}
