<?php

namespace Tests\Feature;

use Tests\Base;
use App\Models\Client;
use App\Models\Product;
use Inertia\Testing\Assert;
use App\Models\InboundDelivery;
use App\Models\InboundDeliveryDetail;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InboundDeliveryTest extends Base
{
    use RefreshDatabase;
    
    /** @test */
    public function can_visit_inbound_delivery_list_page()
    {
        InboundDelivery::factory(5)->create();

        $this->get(route('inbounds.index'))
            ->assertInertia(function (Assert $page) {
                return $page->component('InboundDelivery/Index')
                    ->has('data.data', 5);
            });
    }

    /** @test */
    public function can_visit_inbound_delivery_create_page()
    {
        $this->get(route('inbounds.create'))
            ->assertInertia(function (Assert $page) {
                return $page->component('InboundDelivery/Form');
            });
    }

    /** @test */
    public function can_create_new_inbound_delivery()
    {
        $client = Client::factory()->create();
        $data = InboundDelivery::factory([
            'client_code' => $client->code
        ])->raw();

        $this->assertDatabaseCount('inbound_deliveries', 0);
        
        $this->post(route('inbounds.store'), $data);

        $this->assertDatabaseCount('inbound_deliveries', 1);
    }

    /** @test */
    public function can_validate_form_request()
    {
        $data = InboundDelivery::factory([
            'reference' => '',
            'client_code' => '',
            'arrival_date' => '',
            'po_date' => '',
            'notes' => '',
            'status' => ''
        ])->raw();

        $this->post(route('inbounds.store'), $data)
            ->assertSessionHasErrors();
    }

    /** @test */
    public function can_view_existing_inbound_delivery()
    {
        $inbound_delivery = InboundDelivery::factory()->create();

        $this->get(route('inbounds.show', $inbound_delivery))
            ->assertInertia(function (Assert $page) {
                return $page->component('InboundDelivery/Detail')
                    ->has('inbound');
            });
    }

    /** @test */
    public function can_update_existing_inbound_delivery()
    {
        $data = [
            'reference' => 'InboundDelivery A'
        ];

        $inbound_delivery = InboundDelivery::factory($data)->create();

        $this->assertDatabaseHas('inbound_deliveries', $data);

        $new_data = [
            'reference' => 'InboundDelivery B',
        ];

        $new_inbound_delivery = InboundDelivery::factory(array_merge($new_data, [
            'client_code' => $inbound_delivery->client->code
        ]))->raw();

        $this->put(route('inbounds.update', $inbound_delivery), $new_inbound_delivery);

        $this->assertDatabaseHas('inbound_deliveries', $new_data);
    }

    /** @test */
    public function can_delete_existing_inbound_delivery()
    {
        $inbound_delivery = InboundDelivery::factory()->create();

        $this->assertDatabaseCount('inbound_deliveries', 1);

        $this->delete(route('inbounds.destroy', $inbound_delivery));

        $this->assertDatabaseCount('inbound_deliveries', 0);
        $this->assertDatabaseMissing('inbound_deliveries', ['id' => $inbound_delivery->id]);
    }

    /** @test */
    public function can_add_inbound_delivery_detail()
    {
        $inbound_delivery = InboundDelivery::factory()->create();

        $product = Product::factory([
            'client_id' => $inbound_delivery->client_id
        ])->create();

        $data = InboundDeliveryDetail::factory([
            'inbound_delivery_id' => $inbound_delivery->id,
            'code' => $product->code,
            'base_quantity' => 100
        ])->raw();

        $this->assertCount(0, $inbound_delivery->details);

        $data = $this->post(route('inbound_details.store', $data));
        
        $this->assertCount(1, $inbound_delivery->fresh()->details);
    }

    /** @test */
    public function can_update_existing_inbound_delivery_detail()
    {
        $inbound_delivery = InboundDelivery::factory()->create();

        $product = Product::factory([
            'client_id' => $inbound_delivery->client_id
        ])->create();

        $detail = InboundDeliveryDetail::factory([
            'inbound_delivery_id' => $inbound_delivery->id,
            'product_id' => $product->id,
            'base_quantity' => 100
        ])->create();
        
        $new_data = array_merge($detail->toArray(), [
            'code' => $product->code,
            'base_quantity' => 200
        ]);

        $this->assertDatabaseHas('inbound_delivery_details', ['base_quantity' => 100]);

        $this->put(route('inbound_details.update', $detail), $new_data);

        $this->assertDatabaseHas('inbound_delivery_details', ['base_quantity' => 200]);
    }

    /** @test */
    public function can_delete_existing_inbound_delivery_detail()
    {
        $detail = InboundDeliveryDetail::factory()->create();

        $this->assertDatabaseCount('inbound_delivery_details', 1);

        $this->delete(route('inbound_details.destroy', $detail));
        
        $this->assertDatabaseCount('inbound_delivery_details', 0);
        $this->assertDatabaseMissing('inbound_delivery_details', ['id' => $detail->id]);
    }

    /** @test */
    public function can_validate_detail_form_request()
    {
        $data = InboundDeliveryDetail::factory([
            'inbound_delivery_id' => '',
            'line_id' => '',
            'code' => '',
            'base_quantity' => ''
        ])->raw();

        $this->post(route('inbound_details.store'), $data)
            ->assertSessionHasErrors();
    }
}
