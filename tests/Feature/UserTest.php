<?php

namespace Tests\Feature;

use Tests\Base;
use App\Models\User;
use Inertia\Testing\Assert;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends Base
{
    use RefreshDatabase;
    
    /** @test */
    public function can_visit_user_list_page()
    {
        User::factory(5)->create();

        $this->get(route('users.index'))
            ->assertInertia(function (Assert $page) {
                return $page->component('User/Index')
                    ->has('data.data', 6);
            });
    }

    /** @test */
    public function can_visit_user_create_page()
    {
        $this->get(route('users.create'))
            ->assertInertia(function (Assert $page) {
                return $page->component('User/Form');
            });
    }

    /** @test */
    public function can_create_new_user()
    {
        $data = User::factory([
            'client_id' => null
        ])->raw();

        $this->assertDatabaseCount('users', 1);

         $this->post(route('users.store'), $data);

        $this->assertDatabaseCount('users', 2);
    }

    /** @test */
    public function can_validate_form_request()
    {
        $data = User::factory([
            'name' => '',
            'username' => '',
            'email' => ''
        ])->raw();

        $this->post(route('users.store'), $data)
            ->assertSessionHasErrors();
    }

    /** @test */
    public function can_view_existing_user()
    {
        $user = User::factory()->create();

        $this->get(route('users.show', $user))
            ->assertInertia(function (Assert $page) {
                return $page->component('User/Detail')
                    ->has('user');
            });
    }

    /** @test */
    public function can_update_existing_user()
    {
        $data = [
            'username' => 'User A'
        ];

        $user = User::factory($data)->create();

        $this->assertDatabaseHas('users', $data);

        $new_data = [
            'username' => 'User B',
        ];

        $new_user = User::factory($new_data)->raw();

        $this->put(route('users.update', $user), $new_user);

        $this->assertDatabaseHas('users', $new_data);
    }

    /** @test */
    public function can_delete_existing_user()
    {
        $user = User::factory()->create();

        $this->assertDatabaseCount('users', 2);

        $this->delete(route('users.destroy', $user));

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}
