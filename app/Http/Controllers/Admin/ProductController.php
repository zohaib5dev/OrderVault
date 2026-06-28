<?php
// app/Http/Controllers/Api/ProductController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Product;
use App\Models\Category;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of products
     */
    public function index(Request $request)
    {
        try {
            $query = Product::with(['vendor', 'category'])
                ->where('is_active', true);
            if ($request->has('my') && $request->my) {
                $query->where('vendor_id', Auth::user()->vendor->id);
            }
            if ($request->has('admin_oder') && $request->admin_oder) {
                $query->where('vendor_id', Auth::user()->vendor->id);
            }

            // Filter by vendor
            if ($request->has('vendor_id') && $request->vendor_id) {
                $query->where('vendor_id', $request->vendor_id);
            }

            // Filter by category
            if ($request->has('category_id') && $request->category_id) {
                $query->where('category_id', $request->category_id);
            }

            // Filter by price range
            if ($request->has('min_price') && $request->min_price) {
                $query->where('price', '>=', $request->min_price);
            }
            if ($request->has('max_price') && $request->max_price) {
                $query->where('price', '<=', $request->max_price);
            }

            // Filter by payment type
            if ($request->has('payment_type') && $request->payment_type) {
                $query->where('payment_type', $request->payment_type);
            }

            // Filter by stock status
            if ($request->has('in_stock') && $request->in_stock) {
                $query->where('stock_quantity', '>', 0);
            }

            // Search
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('description', 'LIKE', "%{$search}%")
                        ->orWhere('short_description', 'LIKE', "%{$search}%")
                        ->orWhere('sku', 'LIKE', "%{$search}%");
                });
            }

            // Sort
            $sortBy = $request->sort_by ?? 'created_at';
            $sortOrder = $request->sort_order ?? 'desc';

            // Allowed sort fields
            $allowedSorts = ['name', 'price', 'created_at', 'stock_quantity'];
            if (in_array($sortBy, $allowedSorts)) {
                $query->orderBy($sortBy, $sortOrder);
            } else {
                $query->orderBy('created_at', 'desc');
            }

            $perPage = $request->per_page ?? 20;
            $products = $query->paginate($perPage);

            

            return response($products);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch products',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created product
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:products,slug',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0|gt:price',
            'stock_quantity' => 'required|integer|min:0',
            'sku' => 'nullable|string|unique:products,sku',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string|max:100',
        ]);



        try {
            DB::beginTransaction();

            if (empty($request->sku)) {
                $validated['sku'] = 'SKU-' . strtoupper(Str::random(8));
            }

            if (empty($request->slug)) {
                $validated['slug'] = Str::slug($request->name) . '-' . uniqid();
            }

            $validated['vendor_id'] = Auth::user()->vendor->id;

            $product = Product::create($validated);

            // Handle images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('products', 'public');
                }
            }
            // Log activity


            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Product created successfully',
                'data' => ($product->load(['vendor', 'category']))
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified product
     */
    public function show(Request $request, $id)
    {
        try {
            $product = Product::with([
                'vendor',
                'category',
            ])->findOrFail($id);


            // Get related products
            $relatedProducts = Product::where('category_id', $product->category_id)
                ->where('id', '!=', $product->id)
                ->where('is_active', true)
                ->limit(10)
                ->get()
                ->map(function ($p) {
                    return ($p);
                });

            $formattedProduct = ($product);



            $formattedProduct['related_products'] = $relatedProducts;

            return response($formattedProduct);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified product
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:products,slug',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0|gt:price',
            'stock_quantity' => 'required|integer|min:0',
            'sku' => 'nullable|string|unique:products,sku,' . $product->id,
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string|max:100',
        ]);


        try {
            DB::beginTransaction();

            $product->update($validated);

            // Handle images
            if ($request->hasFile('images')) {
                // Delete old images
                foreach ($product->images as $image) {
                    Storage::disk('public')->delete($image->image_path);
                    $image->delete();
                }

                // Upload new images
                foreach ($request->file('images') as $image) {
                    $path = $image->store('products', 'public');
                }
            }

            // Log activity


            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully',
                'data' => ($product->load(['vendor', 'category']))
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified product
     */
    public function destroy(Request $request, $id)
    {
        try {
            $product = Product::findOrFail($id);

            // Check if product has orders
            if ($product->orderItems()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete product with existing orders'
                ], 400);
            }

            DB::beginTransaction();

            // Delete images
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image->image_path);
                $image->delete();
            }

            // Delete attributes
            $product->attributes()->delete();

            // Delete variations
            $product->variations()->delete();

            // Log activity


            // Delete product
            $product->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get products by vendor
     */
    public function vendorProducts(Request $request, $vendorId)
    {
        try {
            $vendor = Vendor::findOrFail($vendorId);

            $query = Product::with(['category'])
                ->where('vendor_id', $vendorId)
                ->where('is_active', true);

            // Search
            if ($request->has('search') && $request->search) {
                $query->where('name', 'LIKE', "%{$request->search}%");
            }

            // Sort
            $sortBy = $request->sort_by ?? 'created_at';
            $sortOrder = $request->sort_order ?? 'desc';
            $allowedSorts = ['name', 'price', 'created_at', 'stock_quantity'];
            if (in_array($sortBy, $allowedSorts)) {
                $query->orderBy($sortBy, $sortOrder);
            }

            $products = $query->paginate(20);

            return response()->json([
                'success' => true,
                'data' => [
                    'vendor' => $vendor,
                    'products' => $products,
                    'pagination' => [
                        'current_page' => $products->currentPage(),
                        'last_page' => $products->lastPage(),
                        'per_page' => $products->perPage(),
                        'total' => $products->total()
                    ]
                ]
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch vendor products',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get featured products
     */


    /**
     * Search products
     */
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'q' => 'required|string|min:2'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Search query is required',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $query = Product::with(['vendor', 'category', 'images'])
                ->where('is_active', true);

            $searchTerm = $request->q;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('description', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('short_description', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('sku', 'LIKE', "%{$searchTerm}%");
            });

            // Filter by category
            if ($request->has('category_id') && $request->category_id) {
                $query->where('category_id', $request->category_id);
            }

            // Filter by price range
            if ($request->has('min_price') && $request->min_price) {
                $query->where('price', '>=', $request->min_price);
            }
            if ($request->has('max_price') && $request->max_price) {
                $query->where('price', '<=', $request->max_price);
            }

            $perPage = $request->per_page ?? 20;
            $products = $query->paginate($perPage);

             

            return response()->json([
                'success' => true,
                'data' => $products,
                'search_term' => $searchTerm
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Search failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update product stock
     */
    public function updateStock(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'stock_quantity' => 'required|integer|min:0',
            'action' => 'required|in:set,add,subtract'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $product = Product::findOrFail($id);

            $oldStock = $product->stock_quantity;
            $newStock = $oldStock;

            switch ($request->action) {
                case 'set':
                    $newStock = $request->stock_quantity;
                    break;
                case 'add':
                    $newStock = $oldStock + $request->stock_quantity;
                    break;
                case 'subtract':
                    $newStock = max(0, $oldStock - $request->stock_quantity);
                    break;
            }

            $product->stock_quantity = $newStock;
            $product->save();

            // Log stock update


            return response()->json([
                'success' => true,
                'message' => 'Stock updated successfully',
                'data' => [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'old_stock' => $oldStock,
                    'new_stock' => $newStock,
                    'action' => $request->action
                ]
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update stock',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload product images
     */
    public function uploadImage(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_primary' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $product = Product::findOrFail($id);

            $path = $request->file('image')->store('products', 'public');

            $image = $product->images()->create([
                'image_path' => $path,
                'is_primary' => $request->is_primary ?? false
            ]);

            // If this is primary, unset other primary images
            if ($image->is_primary) {
                $product->images()
                    ->where('id', '!=', $image->id)
                    ->update(['is_primary' => false]);
            }



            return response()->json([
                'success' => true,
                'message' => 'Image uploaded successfully',
                'data' => [
                    'image_id' => $image->id,
                    'image_url' => asset('storage/' . $path),
                    'is_primary' => $image->is_primary
                ]
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload image',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete product image
     */
    public function deleteImage(Request $request, $productId, $imageId)
    {
        try {
            $product = Product::findOrFail($productId);
            $image = $product->images()->findOrFail($imageId);

            // Delete file
            Storage::disk('public')->delete($image->image_path);

            // Delete record
            $image->delete();



            return response()->json([
                'success' => true,
                'message' => 'Image deleted successfully'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Image not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete image',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get product categories
     */
    public function categories()
    {
        try {
            $categories = Category::withCount('products')
                ->where('is_active', true)
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $categories
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get product filters (for filter UI)
     */
    public function filters()
    {
        try {
            $filters = [
                'categories' => Category::where('is_active', true)
                    ->select('id', 'name', 'slug')
                    ->orderBy('name')
                    ->get(),
                'vendors' => Vendor::where('is_active', true)
                    ->select('id', 'business_name')
                    ->orderBy('business_name')
                    ->get(),
                'price_range' => [
                    'min' => Product::min('price'),
                    'max' => Product::max('price')
                ],
                'payment_types' => ['one_time', 'subscription'],
                'stock_status' => ['in_stock', 'out_of_stock']
            ];

            return response()->json([
                'success' => true,
                'data' => $filters
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch filters',
                'error' => $e->getMessage()
            ], 500);
        }
    }
 

    /**
     * Get product statistics (for vendor dashboard)
     */
    public function stats(Request $request)
    {
        try {
            $user = $request->user();

            if ($user->role === 'vendor' && $user->vendor) {
                $vendorId = $user->vendor->id;
            } elseif ($user->role === 'admin') {
                $vendorId = $request->vendor_id;
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            $query = Product::where('vendor_id', $vendorId);

            $stats = [
                'total_products' => $query->count(),
                'active_products' => (clone $query)->where('is_active', true)->count(),
                'inactive_products' => (clone $query)->where('is_active', false)->count(),
                'out_of_stock' => (clone $query)->where('stock_quantity', '<=', 0)->count(),
                'low_stock' => (clone $query)->where('stock_quantity', '>', 0)
                    ->where('stock_quantity', '<=', 10)
                    ->count(),
                'featured_products' => (clone $query)->where('is_featured', true)->count(),
                'total_sales' => (clone $query)->withCount('orderItems')->get()->sum('order_items_count'),
                'total_revenue' => (clone $query)->with('orderItems')->get()
                    ->sum(function ($product) {
                        return $product->orderItems->sum('total_price');
                    }),
                'top_products' => (clone $query)
                    ->withCount('orderItems')
                    ->orderBy('order_items_count', 'desc')
                    ->limit(10)
                    ->get()
                    ->map(function ($product) {
                        return [
                            'id' => $product->id,
                            'name' => $product->name,
                            'price' => number_format($product->price, 2),
                            'total_sales' => $product->order_items_count
                        ];
                    })
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch product stats',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
