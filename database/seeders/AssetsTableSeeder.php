<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AssetsTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('assets')->insert([
            [
                'asset_name'       => 'Laptop Dell XPS 13',
                'asset_sn'         => 'SN1001',
                'asset_group_id'   => 1,
                'department_id'    => 1,
                'location_id'      => 1,
                'accountable_party'=> 'Nguyễn Văn A',
                'description'      => 'Laptop phục vụ cho nhân viên IT',
                'warranty_date'    => Carbon::parse('2026-01-15'),
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'asset_name'       => 'Ghế văn phòng Ergonomic',
                'asset_sn'         => 'SN1002',
                'asset_group_id'   => 2,
                'department_id'    => 2,
                'location_id'      => 1,
                'accountable_party'=> 'Trần Thị B',
                'description'      => 'Ghế công thái học cho nhân viên văn phòng',
                'warranty_date'    => Carbon::parse('2026-03-10'),
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'asset_name'       => 'Máy in HP LaserJet',
                'asset_sn'         => 'SN1003',
                'asset_group_id'   => 1,
                'department_id'    => 3,
                'location_id'      => 2,
                'accountable_party'=> 'Phạm Văn C',
                'description'      => 'Máy in laser cho phòng họp',
                'warranty_date'    => Carbon::parse('2026-02-20'),
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'asset_name'       => 'Bàn làm việc gỗ tự nhiên',
                'asset_sn'         => 'SN1004',
                'asset_group_id'   => 2,
                'department_id'    => 2,
                'location_id'      => 3,
                'accountable_party'=> 'Lê Văn D',
                'description'      => 'Bàn làm việc rộng rãi cho nhân viên',
                'warranty_date'    => Carbon::parse('2026-04-05'),
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
        ]);
    }
}
