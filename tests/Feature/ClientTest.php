<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Client;
use Inertia\Testing\Assert;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClientTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function can_visit_client_list_page()
    {
        Client::factory(5)->create();

        $this->get(route('clients.index'))
            ->assertInertia(function (Assert $page) {
                return $page->component('Client/Index')
                    ->has('data.data', 5);
            });
    }

    /** @test */
    public function can_visit_client_create_page()
    {
        $this->get(route('clients.create'))
            ->assertInertia(function (Assert $page) {
                return $page->component('Client/Form');
            });
    }

    /** @test */
    public function can_create_new_client()
    {
        $data = Client::factory()->raw();

        $this->assertDatabaseCount('clients', 0);
        
        $this->post(route('clients.store'), $data);

        $this->assertDatabaseCount('clients', 1);
    }

    /** @test */
    public function can_validate_form_request()
    {
        $data = Client::factory([
            'code' => '',
            'name' => '',
            'address_1' => '',
            'address_2' => ''
        ])->raw();

        $this->post(route('clients.store'), $data)
            ->assertSessionHasErrors();
    }

    /** @test */
    public function can_view_existing_client()
    {
        $client = Client::factory()->create();

        $this->get(route('clients.show', $client))
            ->assertInertia(function (Assert $page) {
                return $page->component('Client/Detail')
                    ->has('client');
            });
    }

    /** @test */
    public function can_update_existing_client()
    {
        $data = [
            'code' => 'Client A'
        ];

        $client = Client::factory($data)->create();

        $this->assertDatabaseHas('clients', $data);

        $new_data = [
            'code' => 'Client B'
        ];

        $new_client = Client::factory($new_data)->raw();

        $this->put(route('clients.update', $client), $new_client);

        $this->assertDatabaseHas('clients', $new_data);
    }

    /** @test */
    public function can_delete_existing_client()
    {
        $client = Client::factory()->create();

        $this->assertDatabaseCount('clients', 1);

        $this->delete(route('clients.destroy', $client));

        $this->assertDatabaseCount('clients', 0);
        $this->assertDatabaseMissing('clients', ['id' => $client->id]);
    }
}
