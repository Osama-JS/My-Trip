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
                        <div class="dropdown">
                            <button type="button" class="btn btn-light btn-sm rounded-circle border-0 shadow-sm" data-bs-toggle="dropdown" aria-expanded="false" style="width:32px; height:32px; padding:0; display:inline-flex; align-items:center; justify-content:center;">
                                <i class="fas fa-ellipsis-v text-muted"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 py-2">
                                <a class="dropdown-item py-2 px-3 d-flex align-items-center" href="javascript:void(0);" onclick="editCategory(' . $category->id . ')"><i class="fas fa-pencil-alt text-primary me-3 w-15px"></i> '.__('Edit').'</a>
                                <div class="dropdown-divider my-1"></div>
                                <a class="dropdown-item text-danger py-2 px-3 d-flex align-items-center" href="javascript:void(0);" onclick="deleteCategory(' . $category->id . ')"><i class="fa fa-trash text-danger me-3 w-15px"></i> '.__('Delete').'</a>
                            </div>
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
