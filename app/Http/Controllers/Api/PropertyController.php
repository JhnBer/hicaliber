<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SearchPropertyRequest;
use App\Models\Property;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function search(SearchPropertyRequest $request): JsonResponse
    {
        $filters = $request->validated();

        $properties = Property::search($filters)
            ->orderBy('id')
            ->latest()
            ->paginate(15);

        return response()->json($properties);
    }

    public function seed(Request $request): Response
    {
        Property::factory(100)->create();

        return response()->noContent();
    }

    public function clean(Request $request): Response
    {
        Property::query()->truncate();

        return response()->noContent();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Property $property)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Property $property)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Property $property)
    {
        //
    }
}
