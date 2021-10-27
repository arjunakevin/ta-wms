<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Inertia\Testing\Assert;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_visit_login_page()
    {
        $this->get(route('login'))
            ->assertInertia(function (Assert $page) {
                return $page->component('Login');
            });
    }

    /** @test */
    public function login_with_invalid_data()
    {
        $data = [
            'username' => '',
            'password' => '',
        ];

        $this->post(route('login.attempt'), $data)
            ->assertSessionHasErrors(['username', 'password']);
    }

    /** @test */
    public function login_with_invalid_credentials()
    {
        $data = [
            'username' => 'username',
            'password' => 'password',
        ];

        $this->post(route('login.attempt'), $data)
            ->assertSessionHasErrors([
                'auth' => 'Invalid credentials.'
            ]);
    }
    
    /** @test */
    public function login()
    {
        User::factory([
            'username' => 'username',
            'password' => bcrypt('password')
        ])->create();

        $data = [
            'username' => 'username',
            'password' => 'password',
        ];

        $this->post(route('login.attempt'), $data)
            ->assertSessionDoesntHaveErrors()
            ->assertRedirect(route('home'));
    }
}
