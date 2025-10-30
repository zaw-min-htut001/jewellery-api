<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\ProductService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductCollection;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductController extends Controller
{
    use ApiResponseTrait; 

    public function __construct(protected ProductService $service) {}

    public function index(): JsonResponse
    {
        $products = $this->service->getAll();
        return $this->collection(new ProductCollection($products));
    }

    public function show(Product $product): JsonResponse
    {
        try {
            $product = $this->service->show($product->id);
            return $this->resource(new ProductResource($product));
        } catch (ModelNotFoundException $e) {
            return $this->notFound('Product not found');
        }
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = $this->service->create($request->validated());
        return $this->created(new ProductResource($product));
    }

    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $product = $this->service->update($product, $request->validated());
        return $this->resource(new ProductResource($product), 'Product updated');
    }

    public function destroy(Product $product): JsonResponse
    {
        $this->service->delete($product);
        return $this->noContent();
    }
}
