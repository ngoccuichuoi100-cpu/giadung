<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class SyncLocationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'locations:sync {--fresh : Tải lại dữ liệu mới nhất từ API provinces.open-api.vn}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Đồng bộ dữ liệu Tỉnh/Thành, Quận/Huyện, Phường/Xã từ Vietnam Provinces API (provinces.open-api.vn)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $jsonPath = storage_path('app/vietnam_locations.json');
        $forceFresh = $this->option('fresh');

        if ($forceFresh || !file_exists($jsonPath)) {
            $this->info('Đang kết nối tới https://provinces.open-api.vn/api/?depth=3...');
            
            try {
                $response = Http::timeout(60)
                    ->withOptions(['verify' => false])
                    ->get('https://provinces.open-api.vn/api/?depth=3');

                if ($response->successful()) {
                    file_put_contents($jsonPath, $response->body());
                    $this->info('Đã tải và lưu trữ dữ liệu vào ' . $jsonPath);
                } else {
                    $this->error('Không thể tải từ API (HTTP ' . $response->status() . ').');
                    if (!file_exists($jsonPath)) {
                        return 1;
                    }
                    $this->warn('Sử dụng dữ liệu offline có sẵn.');
                }
            } catch (\Exception $e) {
                $this->warn('Lỗi kết nối API: ' . $e->getMessage());
                if (!file_exists($jsonPath)) {
                    return 1;
                }
                $this->warn('Sử dụng dữ liệu offline có sẵn.');
            }
        }

        $this->info('Đang phân tích cấu trúc dữ liệu...');
        $data = json_decode(file_get_contents($jsonPath), true);
        if (!$data || !is_array($data)) {
            $this->error('Dữ liệu JSON không hợp lệ.');
            return 1;
        }

        $now = now();
        $provinceRows = [];
        $districtRows = [];
        $wardRows = [];

        foreach ($data as $p) {
            $pCode = sprintf("%02d", $p['code']);
            $provinceRows[] = [
                'code' => $pCode,
                'name' => $p['name'],
                'created_at' => $now,
                'updated_at' => $now,
            ];

            foreach ($p['districts'] ?? [] as $d) {
                $dCode = sprintf("%03d", $d['code']);
                $districtRows[] = [
                    'code' => $dCode,
                    'province_code' => $pCode,
                    'name' => $d['name'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                foreach ($d['wards'] ?? [] as $w) {
                    $wCode = sprintf("%05d", $w['code']);
                    $wardRows[] = [
                        'code' => $wCode,
                        'district_code' => $dCode,
                        'name' => $w['name'],
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
        }

        $this->info('Đang cập nhật CSDL MySQL (' . count($provinceRows) . ' tỉnh, ' . count($districtRows) . ' huyện, ' . count($wardRows) . ' xã)...');

        DB::beginTransaction();
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            DB::table('provinces')->upsert($provinceRows, ['code'], ['name', 'updated_at']);

            foreach (array_chunk($districtRows, 500) as $chunk) {
                DB::table('districts')->upsert($chunk, ['code'], ['province_code', 'name', 'updated_at']);
            }

            foreach (array_chunk($wardRows, 1000) as $chunk) {
                DB::table('wards')->upsert($chunk, ['code'], ['district_code', 'name', 'updated_at']);
            }

            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            DB::commit();

            $this->info('✅ Đồng bộ thành công 63 Tỉnh/Thành, ' . count($districtRows) . ' Quận/Huyện, ' . count($wardRows) . ' Phường/Xã vào cơ sở dữ liệu!');
            return 0;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Lỗi lưu CSDL: ' . $e->getMessage());
            return 1;
        }
    }
}
