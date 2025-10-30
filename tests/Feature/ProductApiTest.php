<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $token;

    public function setUp(): void
    {
        parent::setUp();

        // Create a test user
        $this->user = User::factory()->create([
            'email' => 'admin@uaejewellery.ae',
            'password' => bcrypt('password'),
        ]);

        // Login and extract token
        $response = $this->postJson('/api/login', [
            'email' => 'admin@uaejewellery.ae',
            'password' => 'password',
        ]);

        $this->token = $response->json('data.token');
    }

    #[Test]
    public function unauthenticated_user_cannot_access_protected_endpoints(): void
    {
        $response = $this->getJson('/api/products');
        $response->assertStatus(401);
    }

    #[Test]
    public function authenticated_user_can_list_products(): void
    {
        Product::factory()->withVariants()->create();

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
                        ->getJson('/api/products');
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        'data' => [            
                            ['id', 'name', 'slug', 'base_price', 'variants_count']
                        ]
                    ]
                ]);
    }

    #[Test]
    public function authenticated_user_can_update_a_product(): void
    {
        $product = Product::factory()->create(['name' => 'Old Name']);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
                        ->putJson("/api/products/{$product->id}", [
                            'name' => 'Updated Product Name',
                            'slug' => 'updated-product-name', // added
                            'description' => 'Updated description',
                            'base_price' => 1999.99,
                        ]);

        $response->assertStatus(200)
                ->assertJsonPath('data.name', 'Updated Product Name');

        $this->assertDatabaseHas('products', ['name' => 'Updated Product Name']);
    }

    #[Test]
    public function authenticated_user_can_delete_a_product(): void
    {
        $product = Product::factory()->withVariants()->create();

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
                        ->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(204);

        // use appropriate check depending on soft/hard delete
        $this->assertSoftDeleted('products', ['id' => $product->id]);
        // or: $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    #[Test]
    public function authenticated_user_can_create_product_with_variants(): void
    {
        $payload = [
            'name' => 'Emirati Gold Bangle',
            'description' => 'Handcrafted 22K gold bangle with UAE motifs',
            'base_price' => 2499.99,
            'slug' => 'emirati-gold-bangle',
            'variants' => [
                [
                    'carat' => '22K',
                    'metal_type' => 'gold',
                    'price' => 2899.99,
                    'stock' => 3,
                    'sku' => 'BANGLE-UAE-22K'
                ],
                [
                    'carat' => '18K',
                    'metal_type' => 'white_gold',
                    'price' => 1999.99,
                    'stock' => 5,
                    'sku' => 'BANGLE-UAE-18K-WG'
                ]
            ]
        ];

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
                         ->postJson('/api/products', $payload);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'data' => [
                         'id', 'name', 'slug', 'variants_count',
                         'variants' => [['sku', 'carat']]
                     ]
                 ]);

        $this->assertDatabaseHas('products', ['slug' => 'emirati-gold-bangle']);
        $this->assertDatabaseHas('variants', ['sku' => 'BANGLE-UAE-22K']);
    }

    #[Test]
    public function authenticated_user_can_view_a_single_product(): void
    {
        $product = Product::factory()->withVariants()->create();


        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
                         ->getJson("/api/products/{$product->id}");

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => ['id', 'name', 'slug']
                 ]);
    }

}
