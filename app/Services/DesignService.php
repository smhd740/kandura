<?php

namespace App\Services;

use App\Models\Design;
use App\Models\DesignImage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class DesignService
{
    /**
     * Get user designs with filters
     */
    public function getUserDesigns(int $userId, array $filters = [])
    {
        $query = Design::where('user_id', $userId)
            ->with(['images', 'measurements', 'designOptions']);

        // Search
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name->ar', 'like', "%{$search}%")
                    ->orWhere('name->en', 'like', "%{$search}%")
                    ->orWhere('description->ar', 'like', "%{$search}%")
                    ->orWhere('description->en', 'like', "%{$search}%");
            });
        }

        // Filter by price
        if (!empty($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }
        if (!empty($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        // Sort
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        // Paginate
        $perPage = $filters['per_page'] ?? 10;
        return $query->paginate($perPage);
    }

    /**
     * Create design
     */
    public function createDesign(array $data, int $userId)
    {
        // 1. Create design
        $design = Design::create([
            'user_id' => $userId,
            'name' => $data['name'],
            'description' => $data['description'],
            'price' => $data['price'],
        ]);

        // 2. Upload images
        $this->uploadImages($design, $data['images'], $data['primary_image_index'] ?? 0);

        // 3. Attach measurements (sizes)
        $design->measurements()->attach($data['measurement_ids']);

        // 4. Attach design options (optional)
        if (!empty($data['design_option_ids'])) {
            $design->designOptions()->attach($data['design_option_ids']);
        }

        return $design->load(['images', 'measurements', 'designOptions']);
    }

    /**
     * Update design
     */
    public function updateDesign(Design $design, array $data)
    {
        // Update basic fields
        if (isset($data['name'])) $design->name = $data['name'];
        if (isset($data['description'])) $design->description = $data['description'];
        if (isset($data['price'])) $design->price = $data['price'];
        $design->save();

        // Add new images
        if (!empty($data['images'])) {
            $currentOrder = $design->images()->max('order') ?? -1;
            $this->uploadImages($design, $data['images'], null, $currentOrder + 1);
        }

        // Delete images
        if (!empty($data['delete_image_ids'])) {
            $this->deleteImages($design, $data['delete_image_ids']);
        }

        // Change primary image
        if (!empty($data['primary_image_id'])) {
            $design->images()->update(['is_primary' => false]);
            $design->images()->where('id', $data['primary_image_id'])->update(['is_primary' => true]);
        }

        // Update measurements
        if (isset($data['measurement_ids'])) {
            $design->measurements()->sync($data['measurement_ids']);
        }

        // Update design options
        if (isset($data['design_option_ids'])) {
            $design->designOptions()->sync($data['design_option_ids']);
        }

        return $design->load(['images', 'measurements', 'designOptions']);
    }

    /**
     * Delete design
     */
    public function deleteDesign(Design $design)
    {
        // Delete all images from storage
        foreach ($design->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        $design->delete();
    }

    /**
     * Get design by ID
     */
    public function getDesignById(int $id)
    {
        return Design::with(['images', 'measurements', 'designOptions', 'user'])
            ->findOrFail($id);
    }

    /**
     * Upload images
     */
    private function uploadImages(Design $design, array $images, ?int $primaryIndex = 0, int $startOrder = 0)
    {
        foreach ($images as $index => $image) {
            $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('designs', $filename, 'public');

            DesignImage::create([
                'design_id' => $design->id,
                'image_path' => $path,
                'is_primary' => ($primaryIndex !== null && $index === $primaryIndex),
                'order' => $startOrder + $index,
            ]);
        }
    }

    /**
     * Delete specific images
     */
    private function deleteImages(Design $design, array $imageIds)
    {
        $images = $design->images()->whereIn('id', $imageIds)->get();

        foreach ($images as $image) {
            Storage::disk('public')->delete($image->image_path);
            $image->delete();
        }
    }

    public function getMyDesigns(array $filters): LengthAwarePaginator
    {
        // نبدأ من جدول designs
        $query = Design::query();

        // نجيب تصاميم المستخدم المسجّل فقط
        $query->where('user_id', auth()->id());

        // نحمّل العلاقات (الصور، المقاسات، الخيارات، المستخدم)
        $query->with(['images', 'measurements', 'designOptions', 'user']);

        // نطبّق الفلاتر والبحث
        $query = $this->applySearchAndFilters($query, $filters);

        // نرجع النتائج مع pagination (15 تصميم بكل صفحة)
        $perPage = $filters['per_page'] ?? 15;
        return $query->paginate($perPage);
    }

    /**
     * تصفّح تصاميم المستخدمين الآخرين
     */
    public function browseDesigns(array $filters): LengthAwarePaginator
    {
        $query = Design::query();

        // نجيب كل التصاميم إلا تصاميم المستخدم الحالي
        $query->where('user_id', '!=', auth()->id());

        $query->with(['images', 'measurements', 'designOptions', 'user']);

        $query = $this->applySearchAndFilters($query, $filters);

        $perPage = $filters['per_page'] ?? 15;
        return $query->paginate($perPage);
    }

    /**
     * تطبيق البحث والفلاتر على الاستعلام
     * هاي الدالة بتاخد الـ query وبتضيفلها شروط البحث والفلترة
     */
    protected function applySearchAndFilters($query, array $filters)
    {
        // === 1. البحث في الاسم والوصف ===
        if (!empty($filters['search'])) {
            $searchTerm = $filters['search'];

            // نبحث في اسم التصميم أو وصفه
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        // === 2. فلتر السعر الأدنى ===
        if (isset($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }

        // === 3. فلتر السعر الأعلى ===
        if (isset($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        // === 4. فلتر المقاسات (Sizes) ===
        //  المستخدم بدو يشوف تصاميم فيها مقاس XL أو XXL
        if (!empty($filters['measurements'])) {
            $measurementIds = $filters['measurements']; // [1, 2, 3]

            // نجيب التصاميم اللي عندها أي من هالمقاسات
            $query->whereHas('measurements', function ($q) use ($measurementIds) {
                $q->whereIn('measurements.id', $measurementIds);
            });
        }

        // . فلتر خيارات التصميم
        // المستخدم بدو يشوف تصاميم فيها لون أزرق أو قماش قطن
        if (!empty($filters['design_options'])) {
            $optionIds = $filters['design_options']; // [5, 8, 12]

            $query->whereHas('designOptions', function ($q) use ($optionIds) {
                $q->whereIn('design_option_id', $optionIds);
            });
        }

        // 6. فلتر حسب صاحب التصميم
        //  المستخدم بدو يشوف تصاميم مستخدم معيّن
        if (!empty($filters['creator_id'])) {
            $query->where('user_id', $filters['creator_id']);
        }

        //  7. الترتيب
        // الترتيب الافتراضي: حسب تاريخ الإنشاء من الأحدث للأقدم
        $sortBy = $filters['sort_by'] ?? 'created_at'; // name, price, created_at
        $sortOrder = $filters['sort_order'] ?? 'desc'; // asc أو desc

        $query->orderBy($sortBy, $sortOrder);

        return $query;
    }
}
