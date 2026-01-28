<?php

namespace App\Http\Controllers\AdminUI;

use App\Http\Controllers\Controller;
use App\Models\DesignOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DesignOptionController extends Controller
{
    /**
     * Display a listing of design options.
     */
    public function index(Request $request)
    {
        $query = DesignOption::query();

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        // Get paginated results
        $designOptions = $query->orderBy('created_at', 'desc')->paginate(15);

        // Statistics by type
        $colorCount = DesignOption::byType('color')->active()->count();
        $fabricCount = DesignOption::byType('fabric_type')->active()->count();
        $sleeveCount = DesignOption::byType('sleeve_type')->active()->count();
        $domeCount = DesignOption::byType('dome_type')->active()->count();

        return view('admin.design-options.index', compact(
            'designOptions',
            'colorCount',
            'fabricCount',
            'sleeveCount',
            'domeCount'
        ));
    }

    /**
     * Show the form for creating a new design option.
     */
    public function create()
    {
        return view('admin.design-options.create');
    }

    /**
     * Store a newly created design option in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name.en' => 'required|string|max:255',
            'name.ar' => 'nullable|string|max:255',
            'type' => 'required|in:color,fabric_type,sleeve_type,dome_type',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'nullable|boolean',
        ]);

        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('design-options', 'public');
        }

        // Create design option
        DesignOption::create([
            'name' => [
                'en' => $validated['name']['en'],
                'ar' => $validated['name']['ar'] ?? $validated['name']['en'],
            ],
            'type' => $validated['type'],
            'image' => $imagePath,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('admin.design-options.index')
            ->with('success', __('Design option created successfully!'));
    }

    /**
     * Show the form for editing the specified design option.
     */
    public function edit(DesignOption $designOption)
    {
        // Count designs using this option
        $designOption->loadCount('designs');

        return view('admin.design-options.edit', compact('designOption'));
    }

    /**
     * Update the specified design option in storage.
     */
    public function update(Request $request, DesignOption $designOption)
    {
        $validated = $request->validate([
            'name.en' => 'required|string|max:255',
            'name.ar' => 'nullable|string|max:255',
            'type' => 'required|in:color,fabric_type,sleeve_type,dome_type',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'nullable|boolean',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($designOption->image) {
                Storage::disk('public')->delete($designOption->image);
            }
            $imagePath = $request->file('image')->store('design-options', 'public');
            $designOption->image = $imagePath;
        }

        // Update design option
        $designOption->update([
            'name' => [
                'en' => $validated['name']['en'],
                'ar' => $validated['name']['ar'] ?? $validated['name']['en'],
            ],
            'type' => $validated['type'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('admin.design-options.index')
            ->with('success', __('Design option updated successfully!'));
    }

    /**
     * Remove the specified design option from storage.
     */
    public function destroy(DesignOption $designOption)
    {
        // Check if option is used in designs
        $usageCount = $designOption->designs()->count();

        if ($usageCount > 0) {
            return redirect()
                ->route('admin.design-options.index')
                ->with('error', __('Cannot delete this option. It is used in :count design(s).', ['count' => $usageCount]));
        }

        // Delete image if exists
        if ($designOption->image) {
            Storage::disk('public')->delete($designOption->image);
        }

        // Delete the option
        $designOption->delete();

        return redirect()
            ->route('admin.design-options.index')
            ->with('success', __('Design option deleted successfully!'));
    }
}
