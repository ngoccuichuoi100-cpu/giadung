<?php

namespace App\Http\Controllers;

use App\Services\LocationService;
use Illuminate\Http\JsonResponse;

class LocationController extends Controller
{
    protected $locationService;

    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    public function provinces(): JsonResponse
    {
        $provinces = $this->locationService->getProvinces();
        return response()->json($provinces);
    }

    public function districts(string $provinceCode): JsonResponse
    {
        $districts = $this->locationService->getDistricts($provinceCode);
        return response()->json($districts);
    }

    public function wards(string $districtCode): JsonResponse
    {
        $wards = $this->locationService->getWards($districtCode);
        return response()->json($wards);
    }

    public function deliveryEstimate(string $provinceCode, \App\Services\DeliveryService $deliveryService): JsonResponse
    {
        $estimate = $deliveryService->getEstDeliveryRange($provinceCode);
        return response()->json($estimate);
    }
}
