<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository
{
    /**
     * Get all products with variant count
     */
    public function allWithVariantCount(): Collection
    {
        return Product::withCount('variants')->get();
    }

    /**
     * Find product by ID with variants
     */
    public function findWithVariants(int $id): ?Product
    {
        return Product::with('variants')->findOrFail($id);
    }

    /**
     * Create new product
     */
    public function create(array $data): Product
    {
        return Product::create($data);
    }

    /**
     * Update product
     */
    public function update(Product $product, array $data): bool
    {
        return $product->update($data);
    }

    /**
     * Soft delete product 
     */
    public function delete(Product $product): bool
    {
        $product->variants()->delete();
        return $product->delete();
    }
}