<?php

namespace Tests\Feature;

use Tests\Base;
use App\Models\Client;
use App\Models\Product;
use Inertia\Testing\Assert;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductTest extends Base
{
    use RefreshDatabase;
    
    /** @test */
    public function can_visit_product_list_page()
    {
        Product::factory(5)->create();

        $this->get(route('products.index'))
            ->assertInertia(function (Assert $page) {
                return $page->component('Product/Index')
                    ->has('data.data', 5);
            });
    }

    /** @test */
    public function can_visit_product_create_page()
    {
        $this->get(route('products.create'))
            ->assertInertia(function (Assert $page) {
                return $page->component('Product/Form');
            });
    }

    /** @test */
    public function can_create_new_product()
    {
        $client = Client::factory()->create();
        $data = Product::factory([
            'client_code' => $client->code
        ])->raw();

        $this->assertDatabaseCount('products', 0);
        
        $this->post(route('products.store'), $data);

        $this->assertDatabaseCount('products', 1);
    }

    /** @test */
    public function can_validate_form_request()
    {
        $data = Product::factory([
            'code' => '',
            'barcode' => '',
            'client_code' => '',
            'description_1' => '',
            'description_2' => '',
            'is_active' => ''
        ])->raw();

        $this->post(route('products.store'), $data)
            ->assertSessionHasErrors();
    }

    /** @test */
    public function can_view_existing_product()
    {
        $product = Product::factory()->create();

        $this->get(route('products.show', $product))
            ->assertInertia(function (Assert $page) {
                return $page->component('Product/Detail')
                    ->has('product');
            });
    }

    /** @test */
    public function can_update_existing_product()
    {
        $data = [
            'code' => 'Product A'
        ];

        $product = Product::factory($data)->create();

        $this->assertDatabaseHas('products', $data);

        $new_data = [
            'code' => 'Product B',
        ];

        $new_product = Product::factory(array_merge($new_data, [
            'client_code' => $product->client->code
        ]))->raw();

        $this->put(route('products.update', $product), $new_product);

        $this->assertDatabaseHas('products', $new_data);
    }

    /** @test */
    public function can_delete_existing_product()
    {
        $product = Product::factory()->create();

        $this->assertDatabaseCount('products', 1);

        $this->delete(route('products.destroy', $product));

        $this->assertDatabaseCount('products', 0);
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}
