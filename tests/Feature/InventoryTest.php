<?php

namespace Tests\Feature;

use Tests\Base;
use Tests\TestCase;
use App\Models\Inventory;
use Inertia\Testing\Assert;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InventoryTest extends Base
{
    use RefreshDatabase;
    
    /** @test */
    public function can_visit_inventory_list_page()
    {
        Inventory::factory(5)->create();

        $this->get(route('inventories.index'))
            ->assertInertia(function (Assert $page) {
                return $page->component('Inventory/Index');
            });
    }
}
