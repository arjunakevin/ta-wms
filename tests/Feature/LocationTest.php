<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Location;
use Inertia\Testing\Assert;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LocationTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function can_visit_location_list_page()
    {
        Location::factory(5)->create();

        $this->get(route('locations.index'))
            ->assertInertia(function (Assert $page) {
                return $page->component('Location/Index')
                    ->has('data.data', 5);
            });
    }

    /** @test */
    public function can_visit_location_create_page()
    {
        $this->get(route('locations.create'))
            ->assertInertia(function (Assert $page) {
                return $page->component('Location/Form');
            });
    }

    /** @test */
    public function can_create_new_location()
    {
        $data = Location::factory()->raw();

        $this->assertDatabaseCount('locations', 0);
        
        $this->post(route('locations.store'), $data);

        $this->assertDatabaseCount('locations', 1);
    }

    /** @test */
    public function can_validate_form_request()
    {
        $data = Location::factory([
            'code' => '',
            'pick_blocked' => '',
            'put_blocked' => ''
        ])->raw();

        $this->post(route('locations.store'), $data)
            ->assertSessionHasErrors();
    }

    /** @test */
    public function can_view_existing_location()
    {
        $location = Location::factory()->create();

        $this->get(route('locations.show', $location))
            ->assertInertia(function (Assert $page) {
                return $page->component('Location/Detail')
                    ->has('location');
            });
    }

    /** @test */
    public function can_update_existing_location()
    {
        $data = [
            'code' => 'Location A'
        ];

        $location = Location::factory($data)->create();

        $this->assertDatabaseHas('locations', $data);

        $new_data = [
            'code' => 'Location B'
        ];

        $new_location = Location::factory($new_data)->raw();

        $this->put(route('locations.update', $location), $new_location);

        $this->assertDatabaseHas('locations', $new_data);
    }

    /** @test */
    public function can_delete_existing_location()
    {
        $location = Location::factory()->create();

        $this->assertDatabaseCount('locations', 1);

        $this->delete(route('locations.destroy', $location));

        $this->assertDatabaseCount('locations', 0);
        $this->assertDatabaseMissing('locations', ['id' => $location->id]);
    }
}
