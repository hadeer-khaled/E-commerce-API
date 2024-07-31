<?php

namespace Tests\Unit;
use Tests\TestCase;
use Mockery;

use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\User ;
use Spatie\Permission\Models\Role;

use App\Utils\PassportHelper;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_registers_user_successfully()
    {
        Role::create(['name' => 'user']);

        $requestData = [
            'name' => 'Hadeer',
            'email' => 'Hadeer@example.com',
            'password' => '123456789',
            'password_confirmation' => '123456789',
        ];

        $response = $this->postJson(route('register'), $requestData);

        $user = User::first();

        $response->assertStatus(201);

        $response->assertJson([
            'message' => 'user registered successfully',
            'data' => [
                'id' => $user->id, 
                'name' => 'Hadeer',
                'email' => 'Hadeer@example.com',
            ],
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'Hadeer',
            'email' => 'Hadeer@example.com',
        ]);

        $this->assertTrue($user->hasRole('user'));
    }

    public function test_user_can_login_successfully()
    {
        $user = User::factory()->create([
            'email' => 'hadeer@example.com',
            'password' => bcrypt($password = 'password123'),
        ]);
    
        $passportHelperMock = Mockery::mock('alias:' . PassportHelper::class);
        $passportHelperMock->shouldReceive('generateTokens')
            ->once()
            ->with('hadeer@example.com', $password)
            ->andReturn([
                'access_token' => 'fake-access-token',
                'refresh_token' => 'fake-refresh-token',
            ]);
    
        $response = $this->postJson(route('login'), [
            'email' => 'hadeer@example.com',
            'password' => $password,
        ]);
    
        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'user logged in successfully',
                     'data' => [
                         'id' => $user->id,
                         'email' => 'hadeer@example.com',
                         'access_token' => 'fake-access-token',
                         'refresh_token' => 'fake-refresh-token',
                     ],
                 ]);
    
        $this->assertAuthenticatedAs($user);
    }
    public function test_login_fails_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'hadeer@example.com',
            'password' => bcrypt('123456789'),
        ]);

        $response = $this->postJson(route('login'), [
            'email' => 'hadeer@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
                ->assertJson([
                    'message' => "credentials don't match",
                ]);

        $this->assertGuest();
    }

}
