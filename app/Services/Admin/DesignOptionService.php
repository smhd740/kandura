<?php

namespace App\Services\Admin;

use App\Models\DesignOption;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DesignOptionService
{
    /**
     * Get all design options with filters
     */
    public function getAllDesignOptions(array $filters = [])
    {
        $query = DesignOption::query();


        // Search - Case insensitive + Partial match
// Search
if (!empty($filters['search'])) {
    $search = $filters['search'];
    $query->where(function($q) use ($search) {
        $q->whereRaw('LOWER(JSON_EXTRACT(name, "$.en")) LIKE ?', ['%' . strtolower($search) . '%'])
          ->orWhereRaw('LOWER(JSON_EXTRACT(name, "$.ar")) LIKE ?', ['%' . strtolower($search) . '%']);
    });
}

        // Filter by type
        if (!empty($filters['type'])) {
            $query->byType($filters['type']);
        }

        // Filter by active status
        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        // Sort
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->sort($sortBy, $sortOrder);

        // Pagination
        $perPage = $filters['per_page'] ?? 15;

        return $query->paginate($perPage);
    }

    /**
     * Create a new design option
     */
    public function createDesignOption(array $data)
    {
        // Handle image upload
        if (!empty($data['image'])) {
            $data['image'] = $this->uploadImage($data['image']);
        }

        return DesignOption::create($data);
    }

    /**
     * Update design option
     */
    public function updateDesignOption(DesignOption $designOption, array $data)
    {
        // Handle image upload
        if (!empty($data['image'])) {
            // Delete old image
            if ($designOption->image) {
                $this->deleteImage($designOption->image);
            }

            $data['image'] = $this->uploadImage($data['image']);
        }

        $designOption->update($data);

        return $designOption->fresh();
    }

    /**
     * Delete design option
     */
    public function deleteDesignOption(DesignOption $designOption)
    {
        // Delete image
        if ($designOption->image) {
            $this->deleteImage($designOption->image);
        }

        return $designOption->delete();
    }

    /**
     * Get design option by ID
     */
    public function getDesignOptionById(int $id)
    {
        return DesignOption::findOrFail($id);
    }

    /**
     * Toggle active status
     */
    public function toggleActiveStatus(DesignOption $designOption)
    {
        $designOption->update([
            'is_active' => !$designOption->is_active,
        ]);

        return $designOption->fresh();
    }

    /**
     * Upload image
     */
    private function uploadImage($image): string
    {
        $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
        $path = $image->storeAs('design-options', $filename, 'public');

        return $path;
    }

    /**
     * Delete image
     */
    private function deleteImage(string $path): bool
    {
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }

        return false;
    }

    /**
     * Get available types
     */
    public function getAvailableTypes(): array
    {
        return [
            [
                'value' => 'color',
                'label' => ['ar' => 'لون', 'en' => 'Color']
            ],
            [
                'value' => 'fabric_type',
                'label' => ['ar' => 'نوع القماش', 'en' => 'Fabric Type']
            ],
            [
                'value' => 'sleeve_type',
                'label' => ['ar' => 'نوع الكم', 'en' => 'Sleeve Type']
            ],
            [
                'value' => 'dome_type',
                'label' => ['ar' => 'نوع القبة', 'en' => 'Dome Type']
            ],
        ];
    }
}
