<?php

namespace App\Services;

use App\Models\Address;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AddressService
{
    public function getUserAddresses(int $userId, array $filters = []): LengthAwarePaginator
    {
        $query = Address::where('user_id', $userId)->with('city');

        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        if (!empty($filters['city_id'])) {
            $query->city($filters['city_id']);
        }

        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->sort($sortBy, $sortOrder);

        $perPage = $filters['per_page'] ?? 10;

        return $query->paginate($perPage);
    }

    public function createAddress(array $data, int $userId): Address
    {
        $data['user_id'] = $userId;
        $address = Address::create($data);
        $address->load('city');
        return $address;
    }

    public function updateAddress(Address $address, array $data): Address
    {
        $address->update($data);
        $address->load('city');
        return $address->fresh();
    }

    public function deleteAddress(Address $address): bool
    {
        return $address->delete();
    }

    public function getAddressById(int $id): ?Address
    {
        return Address::with('city')->find($id);
    }
}
