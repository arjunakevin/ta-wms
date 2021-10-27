<?php

namespace Tests\Feature;

use Tests\Base;
use App\Models\Location;
use App\Models\Inventory;
use App\Models\GoodReceive;
use Inertia\Testing\Assert;
use App\Models\MovementOrder;
use App\Models\MovementOrderDetail;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MovementOrderTest extends Base
{
    use RefreshDatabase;

    /** @test */
    public function can_visit_movement_order_list_page()
    {
        MovementOrderDetail::factory(5)->create();

        $this->get(route('movement_orders.index'))
            ->assertInertia(function (Assert $page) {
                return $page->component('MovementOrder/Index')
                    ->has('data.data', 5);
            });
    }

    /** @test */
    public function can_search_document()
    {
        $document = GoodReceive::factory()->create();

        Inventory::factory([
            'base_quantity' => 100,
            'pick_quantity' => 0,
            'documentable_type' => get_class($document),
            'documentable_id' => $document->id
        ])->create();

        $this->post(route('movement_orders.document.search'), [
                'id' => $document->id,
                'type' => MovementOrder::TYPE_PUTAWAY
            ])
            ->assertRedirect(route('movement_orders.create', [
                'document_id' => $document->id,
                'type' => MovementOrder::TYPE_PUTAWAY
            ]));
    }

    /** @test */
    public function can_create_new_movement_order()
    {
        $data = MovementOrder::factory([
            'type' => 1,
            'document_id' => GoodReceive::factory()->create()->id
        ])->raw();

        $this->assertDatabaseCount('movement_orders', 0);
        
        $this->post(route('movement_orders.store'), $data);

        $this->assertDatabaseCount('movement_orders', 1);
    }

    /** @test */
    public function can_validate_form_request()
    {
        $data = MovementOrder::factory([
            'reference' => '',
            'date' => ''
        ])->raw();

        $this->post(route('movement_orders.store'), $data)
            ->assertSessionHasErrors();
    }

    /** @test */
    public function can_add_movement_detail()
    {
        // $this->withoutExceptionHandling();
        $good_receive = GoodReceive::factory()
            ->hasInventories(['base_quantity' => 100, 'pick_quantity' => 0])
            ->has(MovementOrder::factory(), 'movement_orders')
            ->create();

        $inventory = $good_receive->inventories->first();
        $movement = $good_receive->movement_orders->first();

        $data = [
            'product_code' => $inventory->product->code,
            'base_quantity' => $inventory->base_quantity,
            'location_code' => Location::factory(['put_blocked' => 0])->create()->code
        ];

        $this->assertDatabaseCount('inventories', 1);
        $this->assertCount(0, $movement->details);

        $res = $this->post(route('movement_order_details.store', $movement), $data);

        $this->assertDatabaseCount('inventories', 2);
        $this->assertCount(1, $movement->fresh()->details);
    }

    /** @test */
    public function can_confirm_movement_order()
    {
        $this->withoutExceptionHandling();
        $movement_order = MovementOrderDetail::factory()->create();
        $source_inventory = $movement_order->source_inventory;
        $destination_inventory = $movement_order->destination_inventory;

        $source_pick_quantity = $source_inventory->pick_quantity;
        $source_base_quantity = $source_inventory->base_quantity;
        $destination_base_quantity = $destination_inventory->base_quantity;
        $destination_put_quantity = $destination_inventory->put_quantity;

        $this->assertEquals(MovementOrderDetail::STATUS_OPEN, $movement_order->status);
        $this->assertEquals($source_pick_quantity, $source_inventory->pick_quantity);
        $this->assertEquals($source_base_quantity, $source_inventory->base_quantity);
        $this->assertEquals($destination_base_quantity, $destination_inventory->base_quantity);
        $this->assertEquals($destination_put_quantity, $destination_inventory->put_quantity);

        $this->post(route('movement_orders.process.confirm'), [
                'id' => [$movement_order->id]
            ])
            ->assertRedirect(route('movement_orders.index'));

        $movement_order->refresh();
        $base_quantity = $movement_order->base_quantity;
        $source_inventory = $movement_order->source_inventory;
        $destination_inventory = $movement_order->destination_inventory;

        $this->assertEquals(MovementOrderDetail::STATUS_CONFIRMED, $movement_order->status);
        $this->assertEquals($source_pick_quantity - $base_quantity, $source_inventory->pick_quantity);
        $this->assertEquals($source_base_quantity - $base_quantity, $source_inventory->base_quantity);
        $this->assertEquals($destination_base_quantity + $base_quantity, $destination_inventory->base_quantity);
        $this->assertEquals($destination_put_quantity - $base_quantity, $destination_inventory->put_quantity);
    }

    /** @test */
    public function can_cancel_movement_order()
    {
        $movement_order = MovementOrderDetail::factory()->create();
        $source_inventory = $movement_order->source_inventory;
        $destination_inventory = $movement_order->destination_inventory;

        $source_pick_quantity = $source_inventory->pick_quantity;
        $source_base_quantity = $source_inventory->base_quantity;
        $destination_base_quantity = $destination_inventory->base_quantity;
        $destination_put_quantity = $destination_inventory->put_quantity;

        $this->assertEquals(MovementOrderDetail::STATUS_OPEN, $movement_order->status);
        $this->assertEquals($source_pick_quantity, $source_inventory->pick_quantity);
        $this->assertEquals($source_base_quantity, $source_inventory->base_quantity);
        $this->assertEquals($destination_base_quantity, $destination_inventory->base_quantity);
        $this->assertEquals($destination_put_quantity, $destination_inventory->put_quantity);

        $this->post(route('movement_orders.process.cancel'), [
                'id' => [$movement_order->id]
            ])
            ->assertRedirect(route('movement_orders.index'));

        $movement_order->refresh();
        $base_quantity = $movement_order->base_quantity;
        $source_inventory = $movement_order->source_inventory;
        $destination_inventory = $movement_order->destination_inventory;

        $this->assertEquals(MovementOrderDetail::STATUS_CANCELED, $movement_order->status);
        $this->assertEquals($source_pick_quantity - $base_quantity, $source_inventory->pick_quantity);
        $this->assertEquals($source_base_quantity, $source_inventory->base_quantity);
        $this->assertEquals($destination_base_quantity, $destination_inventory->base_quantity);
        $this->assertEquals($destination_put_quantity - $base_quantity, $destination_inventory->put_quantity);
    }

    /** @test */
    public function can_print_movement_list()
    {
        $this->assertTrue(true);
    }
}
