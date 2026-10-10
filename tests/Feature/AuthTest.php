<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    private function registrationData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'secret-password',
            'password_confirmation' => 'secret-password',
        ], $overrides);
    }

    public function test_user_can_register(): void
    {
        $this->post(route('register.store'), $this->registrationData())
            ->assertRedirect('/');

        $user = User::where('email', 'jane@example.com')->firstOrFail();
        $this->assertAuthenticatedAs($user);
        $this->assertNotSame('secret-password', $user->password);
    }

    public function test_registration_validates_input(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);

        $this->post(route('register.store'), $this->registrationData([
            'email' => 'taken@example.com',
            'password' => 'short',
            'password_confirmation' => 'different',
        ]))->assertSessionHasErrors(['email', 'password']);

        $this->assertGuest();
    }

    public function test_user_can_log_in_and_is_redirected_to_intended_page(): void
    {
        $user = User::factory()->create();

        $this->post(route('authenticate'), ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect('/');

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $user = User::factory()->create();

        $this->from(route('login'))
            ->post(route('authenticate'), ['email' => $user->email, 'password' => 'wrong'])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_user_can_log_out(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('logout'))
            ->assertRedirect('/');

        $this->assertGuest();
    }
}
