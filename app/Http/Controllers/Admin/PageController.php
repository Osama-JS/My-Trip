<?php

namespace App\Http\Controllers\Admin;

use App\Models\Page;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PageController extends Controller
{
    /**
     * Display a listing of the pages.
     */
    public function index()
    {
        $pages = Page::latest()->get();
        $stats = [
            'total' => Page::count(),
            'active' => Page::where('status', true)->count(),
            'inactive' => Page::where('status', false)->count(),
        ];
        return view('admin.pages.index', compact('pages', 'stats'));
    }

    /**
     * Show the form for creating a new page.
     */
    public function create()
    {
        return view('admin.pages.create');
    }

    /**
     * Store a newly created page in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'slug'     => 'nullable|string|max:255|unique:pages,slug',
            'content_ar' => 'nullable|string',
            'content_en' => 'nullable|string',
        ]);

        $data = $request->all();
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title_en'] ?: $data['title_ar']);
        }

        Page::create($data);

        return redirect()->route('admin.pages.index')
            ->with('success', __('Page created successfully.'));
    }

    /**
     * Show the form for editing the specified page.
     */
    public function edit(Page $page)
    {
        return view('admin.pages.edit', compact('page'));
    }

    /**
     * Update the specified page in storage.
     */
    public function update(Request $request, Page $page)
    {
        $request->validate([
            'title_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'slug'     => 'nullable|string|max:255|unique:pages,slug,' . $page->id,
            'content_ar' => 'nullable|string',
            'content_en' => 'nullable|string',
        ]);

        $data = $request->all();
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title_en'] ?: $data['title_ar']);
        }

        $page->update($data);

        return redirect()->route('admin.pages.index')
            ->with('success', __('Page updated successfully.'));
    }

    /**
     * Remove the specified page from storage.
     */
    public function destroy(Page $page)
    {
        $page->delete();
        return redirect()->route('admin.pages.index')
            ->with('success', __('Page deleted successfully.'));
    }

    /**
     * Toggle the status of a page.
     */
    public function toggleStatus(Page $page)
    {
        $page->update(['status' => !$page->status]);
        return response()->json(['success' => true, 'message' => __('Status updated.')]);
    }
}
