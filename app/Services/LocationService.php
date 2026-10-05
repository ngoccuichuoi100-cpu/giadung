<?php

namespace App\Services;

use App\Models\Province;
use App\Models\District;
use App\Models\Ward;

class LocationService
{
    public function getProvinces()
    {
        return \Illuminate\Support\Facades\Cache::rememberForever('locations_provinces', function () {
            return Province::orderBy('name', 'asc')->get();
        });
    }

    public function getDistricts($provinceCode)
    {
        return \Illuminate\Support\Facades\Cache::rememberForever("locations_districts_{$provinceCode}", function () use ($provinceCode) {
            return District::where('province_code', $provinceCode)->orderBy('name', 'asc')->get();
        });
    }

    public function getWards($districtCode)
    {
        return \Illuminate\Support\Facades\Cache::rememberForever("locations_wards_{$districtCode}", function () use ($districtCode) {
            return Ward::where('district_code', $districtCode)->orderBy('name', 'asc')->get();
        });
    }

    public function getLocationNames($provinceCode, $districtCode, $wardCode)
    {
        $province = Province::where('code', $provinceCode)->first();
        $district = District::where('code', $districtCode)->first();
        $ward = Ward::where('code', $wardCode)->first();

        return [
            'province' => $province ? $province->name : '',
            'district' => $district ? $district->name : '',
            'ward' => $ward ? $ward->name : '',
        ];
    }
}
