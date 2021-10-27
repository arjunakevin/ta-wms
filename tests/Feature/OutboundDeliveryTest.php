<?php

namespace Tests\Feature;

use Tests\Base;
use App\Models\Client;
use App\Models\Product;
use Inertia\Testing\Assert;
use App\Models\OutboundDelivery;
use App\Models\OutboundDeliveryDetail;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OutboundDeliveryTest extends Base
{
    use RefreshDatabase;
    
    /** @test */
    public function can_visit_outbound_delivery_list_page()
    {
        OutboundDelivery::factory(5)->create();

        $this->get(route('outbounds.index'))
            ->assertInertia(function (Assert $page) {
                return $page->component('OutboundDelivery/Index')
                    ->has('data.data', 5);
            });
    }

    /** @test */
    public function can_visit_outbound_delivery_create_page()
    {
        $this->get(route('outbounds.create'))
            ->assertInertia(function (Assert $page) {
                return $page->component('OutboundDelivery/Form');
            });
    }

    /** @test */
    public function can_create_new_outbound_delivery()
    {
        $client = Client::factory()->create();
        $data = OutboundDelivery::factory([
            'client_code' => $client->code
        ])->raw();

        $this->assertDatabaseCount('outbound_deliveries', 0);
        
        $this->post(route('outbounds.store'), $data);

        $this->assertDatabaseCount('outbound_deliveries', 1);
    }

    /** @test */
    public function can_validate_form_request()
    {
        $data = OutboundDelivery::factory([
            'reference' => '',
            'client_code' => '',
            'request_delivery_date' => '',
            'destination_name' => '',
            'destination_phone' => '',
            'destination_address' => '',
            'notes' => ''
        ])->raw();

        $this->post(route('outbounds.store'), $data)
            ->assertSessionHasErrors();
    }

    /** @test */
    public function can_visit_edit_outbound_delivery_form()
    {
        $outbound_delivery = OutboundDelivery::factory()->create();

        $this->get(route('outbounds.edit', $outbound_delivery))
            ->assertInertia(function (Assert $page) {
                return $page->component('OutboundDelivery/Form')
                    ->has('outbound');
            });
    }

    /** @test */
    public function can_update_existing_outbound_delivery()
    {
        $data = [
            'reference' => 'OutboundDelivery A'
        ];

        $outbound_delivery = OutboundDelivery::factory($data)->create();

        $this->assertDatabaseHas('outbound_deliveries', $data);

        $new_data = [
            'reference' => 'OutboundDelivery B',
        ];

        $new_outbound_delivery = OutboundDelivery::factory(array_merge($new_data, [
            'client_code' => $outbound_delivery->client->code
        ]))->raw();

        $this->put(route('outbounds.update', $outbound_delivery), $new_outbound_delivery);

        $this->assertDatabaseHas('outbound_deliveries', $new_data);
    }

    /** @test */
    public function can_delete_existing_outbound_delivery()
    {
        $outbound_delivery = OutboundDelivery::factory()->create();

        $this->assertDatabaseCount('outbound_deliveries', 1);

        $this->delete(route('outbounds.destroy', $outbound_delivery));

        $this->assertDatabaseCount('outbound_deliveries', 0);
        $this->assertDatabaseMissing('outbound_deliveries', ['id' => $outbound_delivery->id]);
    }

    /** @test */
    public function can_add_outbound_delivery_detail()
    {
        $outbound_delivery = OutboundDelivery::factory()->create();

        $product = Product::factory([
            'client_id' => $outbound_delivery->client_id
        ])->create();

        $data = OutboundDeliveryDetail::factory([
            'outbound_delivery_id' => $outbound_delivery->id,
            'code' => $product->code,
            'base_quantity' => 100
        ])->raw();

        $this->assertCount(0, $outbound_delivery->details);

        $data = $this->post(route('outbound_details.store', $data));
        
        $this->assertCount(1, $outbound_delivery->fresh()->details);
    }

    /** @test */
    public function can_update_existing_outbound_delivery_detail()
    {
        $outbound_delivery = OutboundDelivery::factory()->create();

        $product = Product::factory([
            'client_id' => $outbound_delivery->client_id
        ])->create();

        $detail = OutboundDeliveryDetail::factory([
            'outbound_delivery_id' => $outbound_delivery->id,
            'product_id' => $product->id,
            'base_quantity' => 100
        ])->create();
        
        $new_data = array_merge($detail->toArray(), [
            'code' => $product->code,
            'base_quantity' => 200
        ]);

        $this->assertDatabaseHas('outbound_delivery_details', ['base_quantity' => 100]);

        $this->put(route('outbound_details.update', $detail), $new_data);

        $this->assertDatabaseHas('outbound_delivery_details', ['base_quantity' => 200]);
    }

    /** @test */
    public function can_delete_existing_outbound_delivery_detail()
    {
        $detail = OutboundDeliveryDetail::factory([
            'base_quantity' => 100,
            'open_quantity' => 100
        ])->create();

        $this->assertDatabaseCount('outbound_delivery_details', 1);

        $this->delete(route('outbound_details.destroy', $detail));
        
        $this->assertDatabaseCount('outbound_delivery_details', 0);
        $this->assertDatabaseMissing('outbound_delivery_details', ['id' => $detail->id]);
    }
}
