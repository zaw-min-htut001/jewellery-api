<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Product;
use App\Models\Variant;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Repositories\ProductRepository;

class ProductService
{
    public function __construct(protected ProductRepository $repository) {}

    /**
     * List all products with variant count
     */
    public function getAll()
    {
        return $this->repository->allWithVariantCount();
    }

    /**
     * Show single product with variants
     */
    public function show(int $id): Product
    {
        return $this->repository->findWithVariants($id);
    }

    /**
     * Create product with variants
     */
    public function create(array $data): Product
    {
        $productData = collect($data)->except('variants')->toArray();

        $product = $this->repository->create($productData);

        if (isset($data['variants'])) {
            $product->variants()->createMany($data['variants']);
        }

        $product->load('variants');
        return $product;
    }

    /**
     * Update a product and sync variants 
     */
    public function update(Product $product, array $data): Product
    {
        DB::transaction(function () use ($product, $data) {
            $productData = collect($data)->except('variants')->toArray();
            $product->update($productData);

            if (!empty($data['variants'])) {
                $incomingVariants = collect($data['variants']);
                $incomingSkus = $incomingVariants->pluck('sku')->filter();

                $product->variants()
                    ->whereNotIn('sku', $incomingSkus)
                    ->update(['deleted_at' => Carbon::now()]);

                // Prepare data for upsert
                $upsertData = $incomingVariants->map(function ($variant) use ($product) {
                    return array_merge($variant, [
                        'product_id' => $product->id,
                        'updated_at' => Carbon::now(),
                        'deleted_at' => null, 
                    ]);
                })->toArray();

                // Upsert variants by SKU
                Variant::upsert(
                    $upsertData,
                    ['sku'], 
                    ['carat', 'metal_type', 'price', 'stock', 'updated_at', 'deleted_at']
                );
            }
        });

        return $product->load('variants');
    }

    /**
     * Soft delete product
     */
    public function delete(Product $product): bool
    {
        return $this->repository->delete($product);
    }
}