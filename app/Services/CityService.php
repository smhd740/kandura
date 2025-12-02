<?php

namespace App\Services;

use App\Models\City;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CityService
{
    public function getActiveCities(array $filters = []): LengthAwarePaginator
    {
        $query = City::active();

        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        $sortBy = $filters['sort_by'] ?? 'id';
        $sortOrder = $filters['sort_order'] ?? 'asc';
        $query->sort($sortBy, $sortOrder);

        $perPage = $filters['per_page'] ?? 5;

        return $query->paginate($perPage);
    }

    public function getAllCities()
    {
        return City::active()->orderBy('id', 'asc')->get();
    }

    public function getCityById(int $id): ?City
    {
        return City::active()->find($id);
    }
}
