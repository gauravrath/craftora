<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserAddress\StoreUserAddressRequest;
use App\Http\Requests\UserAddress\UpdateUserAddressRequest;
use App\Http\Resources\UserAddressResource;
use App\Models\UserAddress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserAddressController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => UserAddressResource::collection($request->user()->addresses()->orderByDesc('is_default')->get()),
        ]);
    }

    public function store(StoreUserAddressRequest $request): JsonResponse
    {
        $address = $request->user()->addresses()->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Address saved successfully.',
            'data' => UserAddressResource::make($address),
        ], 201);
    }

    public function update(UpdateUserAddressRequest $request, UserAddress $address): JsonResponse
    {
        $this->authorize('update', $address);

        $address->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Address updated successfully.',
            'data' => UserAddressResource::make($address->fresh()),
        ]);
    }

    public function destroy(UserAddress $address): JsonResponse
    {
        $this->authorize('delete', $address);

        $address->delete();

        return response()->json([
            'success' => true,
            'message' => 'Address deleted successfully.',
        ]);
    }
}
