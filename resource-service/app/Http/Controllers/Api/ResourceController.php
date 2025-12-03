<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

/**
 * ResourceController - API Controller për menaxhimin e resurseve
 */
class ResourceController extends Controller
{
    /**
     * Merr të gjitha resurset
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $resources = Resource::all();
        
        return response()->json([
            'success' => true,
            'data' => $resources,
            'count' => $resources->count(),
        ]);
    }

    /**
     * Krijon një resurs të ri
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'status' => 'nullable|string|in:active,inactive',
            'cpu_capacity' => 'nullable|integer|min:0|max:100',
            'memory_capacity' => 'nullable|integer|min:0',
            'storage_capacity' => 'nullable|integer|min:0',
            'region' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $resource = Resource::create([
            'name' => $request->name,
            'type' => $request->type,
            'status' => $request->status ?? 'active',
            'cpu_capacity' => $request->cpu_capacity ?? 0,
            'memory_capacity' => $request->memory_capacity ?? 0,
            'storage_capacity' => $request->storage_capacity ?? 0,
            'region' => $request->region ?? 'eu-west',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Resource created successfully',
            'data' => $resource,
        ], 201);
    }

    /**
     * Merr një resurs specifik
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $resource = Resource::find($id);

        if (!$resource) {
            return response()->json([
                'success' => false,
                'message' => 'Resource not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $resource,
        ]);
    }

    /**
     * Përditëson një resurs
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $resource = Resource::find($id);

        if (!$resource) {
            return response()->json([
                'success' => false,
                'message' => 'Resource not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|required|string|max:255',
            'status' => 'sometimes|required|string|in:active,inactive',
            'cpu_capacity' => 'sometimes|nullable|integer|min:0|max:100',
            'memory_capacity' => 'sometimes|nullable|integer|min:0',
            'storage_capacity' => 'sometimes|nullable|integer|min:0',
            'region' => 'sometimes|nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $resource->update($request->only([
            'name', 'type', 'status', 'cpu_capacity',
            'memory_capacity', 'storage_capacity', 'region'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Resource updated successfully',
            'data' => $resource,
        ]);
    }

    /**
     * Fshin një resurs
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $resource = Resource::find($id);

        if (!$resource) {
            return response()->json([
                'success' => false,
                'message' => 'Resource not found',
            ], 404);
        }

        $resource->delete();

        return response()->json([
            'success' => true,
            'message' => 'Resource deleted successfully',
        ]);
    }
}

