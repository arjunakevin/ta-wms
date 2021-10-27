<?php

namespace Tests\Feature;

use Tests\Base;
use App\Models\GoodReceive;
use Inertia\Testing\Assert;
use App\Models\InboundDelivery;
use App\Models\GoodReceiveDetail;
use App\Models\InboundDeliveryDetail;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GoodReceiveTest extends Base
{
    use RefreshDatabase;
    
    /** @test */
    public function can_visit_good_receive_list_page()
    {
        GoodReceive::factory(5)->create();

        $this->get(route('good_receives.index'))
            ->assertInertia(function (Assert $page) {
                return $page->component('GoodReceive/Index')
                    ->has('data.data', 5);
            });
    }

    /** @test */
    public function can_search_initial_inbound_delivery()
    {
        $inbound = InboundDelivery::factory()->create();

        $this->post(route('good_receives.inbound.search'), [
                'reference' => $inbound->reference,
                'client_code' => $inbound->client->code
            ])
            ->assertRedirect(route('good_receives.create', $inbound));
    }

    /** @test */
    public function can_create_new_good_receive()
    {
        $inbound = InboundDelivery::factory()
            ->hasDetails(2, [
                'base_quantity' => 100,
                'open_quantity' => 100
            ])
            ->create();

        $data = GoodReceive::factory([
            'inbound_delivery_id' => $inbound->id
        ])->raw();

        $this->assertDatabaseCount('good_receives', 0);
        $this->assertEquals(InboundDelivery::STATUS_UNRECEIVED, $inbound->status);
        $this->assertEquals(200, $inbound->details->sum('open_quantity'));

        $this->post(route('good_receives.store'), $data);

        $inbound->refresh();

        $this->assertDatabaseCount('good_receives', 1);
        $this->assertEquals(InboundDelivery::STATUS_FULLY_RECEIVED, $inbound->status);
        $this->assertEquals(0, $inbound->details->sum('open_quantity'));
        $this->assertCount(2, GoodReceive::first()->details);
    }

    /** @test */
    public function can_validate_form_request()
    {
        $data = GoodReceive::factory([
            'inbound_delivery_id' => '',
            'reference' => '',
            'receive_date' => '',
            'notes' => '',
            'status' => ''
        ])->raw();

        $this->post(route('good_receives.store'), $data)
            ->assertSessionHasErrors();
    }

    /** @test */
    public function can_visit_edit_good_receive_form()
    {
        $good_receive = GoodReceive::factory()->create();

        $this->get(route('good_receives.edit', $good_receive))
            ->assertInertia(function (Assert $page) {
                return $page->component('GoodReceive/Form')
                    ->has('good_receive');
            });
    }

    /** @test */
    public function can_update_existing_good_receive()
    {
        $data = [
            'reference' => 'GoodReceive A'
        ];

        $good_receive = GoodReceive::factory($data)->create();

        $this->assertDatabaseHas('good_receives', $data);

        $new_data = [
            'reference' => 'GoodReceive B',
        ];

        $new_good_receive = GoodReceive::factory($new_data)->raw();

        $this->put(route('good_receives.update', $good_receive), $new_good_receive);

        $this->assertDatabaseHas('good_receives', $new_data);
    }

    /** @test */
    public function can_delete_existing_good_receive()
    {
        $inbound = InboundDelivery::factory()->create();

        $inbound_detail = InboundDeliveryDetail::factory([
            'inbound_delivery_id' => $inbound->id,
            'base_quantity' => 100,
            'open_quantity' => 0
        ])->create();

        $good_receive = GoodReceive::factory([
                'inbound_delivery_id' => $inbound->id,
            ])
            ->hasDetails(1, [
                'inbound_detail_id' => $inbound_detail->id,
                'base_quantity' => 100
            ])
            ->create();

        $this->assertDatabaseCount('good_receives', 1);
        $this->assertEquals(0, $inbound->details->sum('open_quantity'));

        $this->delete(route('good_receives.destroy', $good_receive));

        $inbound->refresh();

        $this->assertDatabaseCount('good_receives', 0);
        $this->assertDatabaseMissing('good_receives', ['id' => $good_receive->id]);
        $this->assertNotEquals(InboundDelivery::STATUS_FULLY_RECEIVED, $inbound->status);
        $this->assertEquals(100, $inbound->details->sum('open_quantity'));
    }

    /** @test */
    public function can_visit_item_check_page()
    {
        $good_receive = GoodReceive::factory()->create();

        $this->get(route('good_receives.check', $good_receive))
            ->assertInertia(function (Assert $page) {
                return $page->component('GoodReceive/Check');
            });
    }

    /** @test */
    public function can_validate_item_check_request()
    {
        $good_receive_detail = GoodReceiveDetail::factory()->create();
        $data = [
            'product_code' => '',
            'base_quantity' => ''
        ];

        $this->post(route('good_receives.check', $good_receive_detail->good_receive), $data)
            ->assertSessionHasErrors();
    }

    /** @test */
    public function can_submit_item_check()
    {
        $good_receive_detail = GoodReceiveDetail::factory([
            'base_quantity' => 100,
            'open_check_quantity' => 100
        ])->create();

        $data = [
            'product_code' => $good_receive_detail->inbound_delivery_detail->product->code,
            'base_quantity' => 100
        ];

        $this->assertEquals(100, $good_receive_detail->open_check_quantity);

        $this->post(route('good_receives.check', $good_receive_detail->good_receive), $data);

        $this->assertEquals(0, $good_receive_detail->refresh()->open_check_quantity);
        $this->assertEquals(GoodReceive::STATUS_FULLY_CHECKED, $good_receive_detail->good_receive->status);
    }
    
    /** @test */
    public function can_receive_good_receive()
    {
        $good_receive = GoodReceive::factory([
                'status' => GoodReceive::STATUS_FULLY_CHECKED
            ])
            ->hasDetails([
                'base_quantity' => 100,
                'receive_quantity' => 0,
                'open_check_quantity' => 0
            ])
            ->create();

        $this->assertEquals(GoodReceive::STATUS_FULLY_CHECKED, $good_receive->status);
        $this->assertEquals(0, $good_receive->details->sum('receive_quantity'));
        $this->assertEquals(0, $good_receive->inventories->sum('base_quantity'));

        $this->post(route('good_receives.receive', $good_receive));

        $good_receive->refresh();
        
        $this->assertEquals(GoodReceive::STATUS_RECEIVED, $good_receive->status);
        $this->assertEquals(100, $good_receive->details->sum('receive_quantity'));
        $this->assertEquals(100, $good_receive->inventories->sum('base_quantity'));
    }
}
