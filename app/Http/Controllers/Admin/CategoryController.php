<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        try {
            $vendorId = Auth::user()->vendor->id;

            $query = Category::where('vendor_id', $vendorId);
            if ($request->filled('all')) {
                $query = Category::query();
            }


            // Search by name or description
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('description', 'LIKE', "%{$search}%");
                });
            }

            // Filter by status
            if ($request->filled('status')) {
                if ($request->status === 'active') {
                    $query->where('is_active', true);
                } elseif ($request->status === 'inactive') {
                    $query->where('is_active', false);
                }
            }

            // Sort
            switch ($request->sort) {
                case 'newest':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'oldest':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'name':
                default:
                    $query->orderBy('name', 'asc');
                    break;
            }

            // Pagination
            $perPage = $request->per_page ?? 10;
            $categories = $query->paginate($perPage);

            // Stats
            $stats = [
                'total' => Category::where('vendor_id', $vendorId)->count(),
                'active' => Category::where('vendor_id', $vendorId)->where('is_active', true)->count(),
                'inactive' => Category::where('vendor_id', $vendorId)->where('is_active', false)->count(),
            ];

            return response()->json([
                'data' => $categories,
                'stats' => $stats,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request)
    {
        try {
            $vendorId = Auth::user()->vendor->id;

            $validator = Validator::make($request->all(), [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('categories', 'name')->where(function ($query) use ($vendorId) {
                        return $query->where('vendor_id', $vendorId);
                    }),
                ],
                'description' => 'nullable|string|max:1000',
                'is_active' => 'boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $category = Category::create([
                'vendor_id' => $vendorId,
                'name' => $request->name,
                'description' => $request->description,
                'is_active' => $request->is_active ?? true,
            ]);

            return response()->json([
                'message' => 'Category created successfully',
                'data' => $category
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create category',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Update the specified category.
     */
    public function update(Request $request, $id)
    {
        try {
            $vendorId = Auth::user()->vendor->id;

            $category = Category::where('vendor_id', $vendorId)
                ->where('id', $id)
                ->firstOrFail();

            $validator = Validator::make($request->all(), [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('categories', 'name')
                        ->where('vendor_id', $vendorId)
                        ->ignore($category->id),
                ],
                'description' => 'nullable|string|max:1000',
                'is_active' => 'boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $category->update([
                'name' => $request->name,
                'description' => $request->description,
                'is_active' => $request->is_active ?? $category->is_active,
            ]);

            return response()->json([
                'message' => 'Category updated successfully',
                'data' => $category
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update category',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    /**
     * Remove the specified category.
     */
    public function destroy($id)
    {
        try {
            $vendorId = Auth::user()->vendor->id;

            $category = Category::where('vendor_id', $vendorId)
                ->where('id', $id)
                ->firstOrFail();

            // Check if category has products
            if ($category->products()->count() > 0) {
                return response()->json([
                    'message' => 'Cannot delete category with associated products',
                    'products_count' => $category->products()->count()
                ], 422);
            }

            $category->delete();

            return response()->json([
                'message' => 'Category deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete category',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function getActive()
    {
        $vendorId = Auth::user()->vendor->id;

        $query = Category::where('vendor_id', $vendorId)->where('is_active', true)->get();
        return response()->json([
            'data' => $query
        ], 200);
    }
}
