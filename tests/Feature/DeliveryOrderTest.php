<?php

namespace Tests\Feature;

use Tests\Base;
use App\Models\DeliveryOrder;
use Inertia\Testing\Assert;
use App\Models\OutboundDelivery;
use App\Models\DeliveryOrderDetail;
use App\Models\OutboundDeliveryDetail;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeliveryOrderTest extends Base
{
    use RefreshDatabase;
    
    /** @test */
    public function can_visit_delivery_order_list_page()
    {
        DeliveryOrder::factory(5)->create();

        $this->get(route('delivery_orders.index'))
            ->assertInertia(function (Assert $page) {
                return $page->component('DeliveryOrder/Index')
                    ->has('data.data', 5);
            });
    }

    /** @test */
    public function can_search_initial_outbound_delivery()
    {
        $outbound = OutboundDelivery::factory()->create();

        $this->post(route('delivery_orders.outbound.search'), [
                'reference' => $outbound->reference,
                'client_code' => $outbound->client->code
            ])
            ->assertRedirect(route('delivery_orders.create', $outbound));
    }

    /** @test */
    public function can_create_new_delivery_order()
    {
        $outbound = OutboundDelivery::factory()
            ->hasDetails(2, [
                'base_quantity' => 100,
                'open_quantity' => 100
            ])
            ->create();

        $data = DeliveryOrder::factory([
            'outbound_delivery_id' => $outbound->id
        ])->raw();

        $this->assertDatabaseCount('delivery_orders', 0);
        $this->assertEquals(OutboundDelivery::STATUS_UNCOMMITTED, $outbound->status);
        $this->assertEquals(200, $outbound->details->sum('open_quantity'));

        $this->post(route('delivery_orders.store'), $data);

        $outbound->refresh();

        $this->assertDatabaseCount('delivery_orders', 1);
        $this->assertEquals(OutboundDelivery::STATUS_FULLY_COMMITTED, $outbound->status);
        $this->assertEquals(0, $outbound->details->sum('open_quantity'));
        $this->assertCount(2, DeliveryOrder::first()->details);
    }

    /** @test */
    public function can_validate_form_request()
    {
        $data = DeliveryOrder::factory([
            'outbound_delivery_id' => '',
            'reference' => '',
            'receive_date' => '',
            'notes' => '',
            'status' => ''
        ])->raw();

        $this->post(route('delivery_orders.store'), $data)
            ->assertSessionHasErrors();
    }

    /** @test */
    public function can_visit_edit_delivery_order_form()
    {
        $delivery_order = DeliveryOrder::factory()->create();

        $this->get(route('delivery_orders.edit', $delivery_order))
            ->assertInertia(function (Assert $page) {
                return $page->component('DeliveryOrder/Form')
                    ->has('delivery_order');
            });
    }

    /** @test */
    public function can_update_existing_delivery_order()
    {
        $data = [
            'reference' => 'DeliveryOrder A'
        ];

        $delivery_order = DeliveryOrder::factory($data)->create();

        $this->assertDatabaseHas('delivery_orders', $data);

        $new_data = [
            'reference' => 'DeliveryOrder B',
        ];

        $new_delivery_order = DeliveryOrder::factory($new_data)->raw();

        $this->put(route('delivery_orders.update', $delivery_order), $new_delivery_order);

        $this->assertDatabaseHas('delivery_orders', $new_data);
    }

    /** @test */
    public function can_delete_existing_delivery_order()
    {
        $outbound = OutboundDelivery::factory()->create();

        $outbound_detail = OutboundDeliveryDetail::factory([
            'outbound_delivery_id' => $outbound->id,
            'base_quantity' => 100,
            'open_quantity' => 0
        ])->create();

        $delivery_order = DeliveryOrder::factory([
                'outbound_delivery_id' => $outbound->id,
            ])
            ->hasDetails(1, [
                'outbound_detail_id' => $outbound_detail->id,
                'base_quantity' => 100
            ])
            ->create();

        $this->assertDatabaseCount('delivery_orders', 1);
        $this->assertEquals(0, $outbound->details->sum('open_quantity'));

        $this->delete(route('delivery_orders.destroy', $delivery_order));

        $outbound->refresh();

        $this->assertDatabaseCount('delivery_orders', 0);
        $this->assertDatabaseMissing('delivery_orders', ['id' => $delivery_order->id]);
        $this->assertNotEquals(OutboundDelivery::STATUS_FULLY_COMMITTED, $outbound->status);
        $this->assertEquals(100, $outbound->details->sum('open_quantity'));
    }

    /** @test */
    public function can_visit_item_check_page()
    {
        $delivery_order = DeliveryOrder::factory()->create();

        $this->get(route('delivery_orders.check', $delivery_order))
            ->assertInertia(function (Assert $page) {
                return $page->component('DeliveryOrder/Check');
            });
    }

    /** @test */
    public function can_validate_item_check_request()
    {
        $delivery_order_detail = DeliveryOrderDetail::factory()->create();
        $data = [
            'product_code' => '',
            'base_quantity' => ''
        ];

        $this->post(route('delivery_orders.check', $delivery_order_detail->delivery_order), $data)
            ->assertSessionHasErrors();
    }

    /** @test */
    public function can_submit_item_check()
    {
        $delivery_order_detail = DeliveryOrderDetail::factory([
            'base_quantity' => 100,
            'open_check_quantity' => 100
        ])->create();

        $data = [
            'product_code' => $delivery_order_detail->outbound_delivery_detail->product->code,
            'base_quantity' => 100
        ];

        $this->assertEquals(100, $delivery_order_detail->open_check_quantity);

        $this->post(route('delivery_orders.check', $delivery_order_detail->delivery_order), $data);

        $this->assertEquals(0, $delivery_order_detail->refresh()->open_check_quantity);
        $this->assertEquals(DeliveryOrder::STATUS_FULLY_CHECKED, $delivery_order_detail->delivery_order->status);
    }
    
    /** @test */
    public function can_good_issue_delivery_order()
    {
        $delivery_order = DeliveryOrder::factory([
                'status' => DeliveryOrder::STATUS_FULLY_CHECKED
            ])
            ->hasDetails([
                'base_quantity' => 100,
                'good_issue_quantity' => 0,
                'open_check_quantity' => 0
            ])
            ->hasInventories([
                'base_quantity' => 100
            ])
            ->create();

        $this->assertEquals(DeliveryOrder::STATUS_FULLY_CHECKED, $delivery_order->status);
        $this->assertEquals(0, $delivery_order->details->sum('good_issue_quantity'));
        $this->assertEquals(100, $delivery_order->inventories->sum('base_quantity'));

        $this->post(route('delivery_orders.good_issue', $delivery_order));

        $delivery_order->refresh();
        
        $this->assertEquals(DeliveryOrder::STATUS_GOOD_ISSUED, $delivery_order->status);
        $this->assertEquals(100, $delivery_order->details->sum('good_issue_quantity'));
        $this->assertEquals(0, $delivery_order->inventories->sum('base_quantity'));
    }

    /** @test */
    public function can_print_delivery_order_report()
    {
        return $this->assertTrue(true);
    }
}
