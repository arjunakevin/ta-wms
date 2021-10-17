<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\GoodReceive;
use Inertia\Testing\Assert;
use App\Models\InboundDelivery;
use App\Models\InboundDeliveryDetail;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GoodReceiveTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function can_visit_good_receive_list_page()
    {
        GoodReceive::factory(5)->create();

        $this->get(route('grs.index'))
            ->assertInertia(function (Assert $page) {
                return $page->component('GoodReceive/Index')
                    ->has('data.data', 5);
            });
    }

    /** @test */
    public function can_visit_good_receive_create_page()
    {
        $inbound = InboundDelivery::factory()->create();
        
        $this->get(route('grs.create', $inbound))
            ->assertInertia(function (Assert $page) {
                return $page->component('GoodReceive/Form');
            });
    }

    /** @test */
    public function can_search_initial_inbound_delivery()
    {
        $inbound = InboundDelivery::factory()->create();

        $this->post(route('grs.inbound.search'), [
                'reference' => $inbound->reference,
                'client_code' => $inbound->client->code
            ])
            ->assertRedirect(route('grs.create', $inbound));
    }

    /** @test */
    public function can_create_new_good_receive()
    {
        $this->withoutExceptionHandling();
        
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

        $this->post(route('grs.store'), $data);

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

        $this->post(route('grs.store'), $data)
            ->assertSessionHasErrors();
    }

    /** @test */
    public function can_view_existing_good_receive()
    {
        $good_receive = GoodReceive::factory()->create();

        $this->get(route('grs.show', $good_receive))
            ->assertInertia(function (Assert $page) {
                return $page->component('GoodReceive/Detail')
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

        $this->put(route('grs.update', $good_receive), $new_good_receive);

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

        $this->delete(route('grs.destroy', $good_receive));

        $inbound->refresh();

        $this->assertDatabaseCount('good_receives', 0);
        $this->assertDatabaseMissing('good_receives', ['id' => $good_receive->id]);
        $this->assertNotEquals(InboundDelivery::STATUS_FULLY_RECEIVED, $inbound->status);
        $this->assertEquals(100, $inbound->details->sum('open_quantity'));
    }
}
