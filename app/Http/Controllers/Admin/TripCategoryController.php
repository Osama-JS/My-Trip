<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TripCategory;
use Illuminate\Http\Request;

class TripCategoryController extends Controller
{
    public function index()
    {
        $stats = [
            'total' => TripCategory::count(),
            'categorized_trips' => \DB::table('trip_category_trip')->distinct('trip_id')->count(),
            'total_links' => \DB::table('trip_category_trip')->count(),
        ];
        return view('admin.categories.index', compact('stats'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
        ]);

        TripCategory::create($data);

        return response()->json([
            'success' => true,
            'message' => __('Category created successfully'),
        ]);
    }

    public function show(TripCategory $trip_category)
    {
        return response()->json([
            'success' => true,
            'category' => $trip_category,
        ]);
    }

    public function update(Request $request, TripCategory $trip_category)
    {
        $data = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
        ]);

        $trip_category->update($data);

        return response()->json([
            'success' => true,
            'message' => __('Category updated successfully'),
        ]);
    }

    public function destroy(TripCategory $trip_category)
    {
        $trip_category->delete();

        return response()->json([
            'success' => true,
            'message' => __('Category deleted successfully'),
        ]);
    }

    public function getData(Request $request)
    {
        $categories = TripCategory::latest()->get();

        return response()->json([
            'data' => $categories->map(function($category, $index) {
                return [
                    'id' => $index + 1,
                    'name_ar' => '<strong class="text-dark">' . $category->name_ar . '</strong>',
                    'name_en' => '<strong class="text-dark">' . $category->name_en . '</strong>',
                    'actions' => '
                        <div class="d-flex align-items-center gap-1">
                            <button type="button" class="act-action-btn" onclick="editCategory(' . $category->id . ')" title="'.__('Edit').'">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="act-action-btn" style="color: #ef4444; background: rgba(239,68,68,0.1); border:none;" onclick="deleteCategory(' . $category->id . ')" title="'.__('Delete').'">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>'
                ];
            })
        ]);
    }

    public function getAll()
    {
        return response()->json(TripCategory::all());
    }
}
