<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Address\StoreAddressRequest;
use App\Http\Requests\Address\UpdateAddressRequest;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use App\Services\AddressService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AddressController extends Controller
{
    use AuthorizesRequests;

    protected AddressService $addressService;

    public function __construct(AddressService $addressService)
    {
        $this->addressService = $addressService;
    }

    public function index(Request $request): JsonResponse
    {
        $filters = [
            'search' => $request->input('search'),
            'city_id' => $request->input('city_id'),
            'sort_by' => $request->input('sort_by', 'created_at'),
            'sort_order' => $request->input('sort_order', 'desc'),
            'per_page' => $request->input('per_page', 10),
        ];

        $addresses = $this->addressService->getUserAddresses(auth()->id(), $filters);

        return response()->json([
            'success' => true,
            'message' => 'Addresses retrieved successfully',
            'data' => AddressResource::collection($addresses),
            'meta' => [
                'current_page' => $addresses->currentPage(),
                'last_page' => $addresses->lastPage(),
                'per_page' => $addresses->perPage(),
                'total' => $addresses->total(),
            ],
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    public function store(StoreAddressRequest $request): JsonResponse
    {
        $this->authorize('create', Address::class);

        $address = $this->addressService->createAddress(
            $request->validated(),
            auth()->id()
        );

        return response()->json([
            'success' => true,
            'message' => 'Address created successfully',
            'data' => new AddressResource($address),
            'timestamp' => now()->toIso8601String(),
        ], 201);
    }

    public function show(Address $address): JsonResponse
    {
        $this->authorize('view', $address);
        $address->load('city');

        return response()->json([
            'success' => true,
            'message' => 'Address retrieved successfully',
            'data' => new AddressResource($address),
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    public function update(UpdateAddressRequest $request, Address $address): JsonResponse
    {
        $this->authorize('update', $address);

        $updatedAddress = $this->addressService->updateAddress($address, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Address updated successfully',
            'data' => new AddressResource($updatedAddress),
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    public function destroy(Address $address): JsonResponse
    {
        $this->authorize('delete', $address);
        $this->addressService->deleteAddress($address);

        return response()->json([
            'success' => true,
            'message' => 'Address deleted successfully',
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
