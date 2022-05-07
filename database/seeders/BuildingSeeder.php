<?php

namespace Database\Seeders;

use App\Enums\Buildingside;
use App\Enums\Buildingtype;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BuildingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('buildings')->insert([
            ['id' => Buildingtype::Trout, 'name' => 'Trout Lodge Guest Room', 'image' => 'guestroom1.jpg;guestroom2.jpg', 'blurb' => __('buildingblurbs.' . Buildingtype::Trout), 'side' => Buildingside::Trout],
            ['id' => Buildingtype::Lakeview, 'name' => 'Lakeview Cabins', 'image' => 'lakeview1.jpg;lakeview2.jpg;lakeview3.jpg', 'blurb' => __('buildingblurbs.' . Buildingtype::Lakeview), 'side' => Buildingside::Trout],
            ['id' => Buildingtype::Forestview, 'name' => 'Forestview Cabins', 'image' => 'forestview1.jpg;forestview2.jpg;forestview3.jpg', 'blurb' => __('buildingblurbs.' . Buildingtype::Forestview), 'side' => Buildingside::Trout],
            ['id' => Buildingtype::Loft, 'name' => 'Trout Lodge Loft Suite', 'image' => 'loftsuite1.jpg;loftsuite2.jpg;loftsuite3.jpg', 'blurb' => __('buildingblurbs.' . Buildingtype::Loft), 'side' => Buildingside::Trout],
            ['id' => Buildingtype::Tent, 'name' => 'Tent Camping', 'image' => null, 'blurb' => __('buildingblurbs.' . Buildingtype::Tent), 'side' => Buildingside::Tent],
            ['id' => Buildingtype::LakewoodYA, 'name' => 'Camp Lakewood Young Adults', 'image' => null, 'blurb' => null, 'side' => Buildingside::Lakewood],
            ['id' => Buildingtype::LakewoodSr, 'name' => 'Camp Lakewood Sr. High Cabin', 'image' => null, 'blurb' => null, 'side' => Buildingside::Lakewood],
            ['id' => Buildingtype::LakewoodJr, 'name' => 'Camp Lakewood Jr. High Cabin', 'image' => null, 'blurb' => null, 'side' => Buildingside::Lakewood],
            ['id' => Buildingtype::LakewoodCabin, 'name' => 'Camp Lakewood Cabins', 'image' => 'cabin171.jpg;cabin172.jpg', 'blurb' => __('buildingblurbs.' . Buildingtype::LakewoodCabin), 'side' => Buildingside::Lakewood],
            ['id' => Buildingtype::Commuter2x, 'name' => 'Commuter (2 meal)', 'image' => null, 'blurb' => null, 'side' => Buildingside::Commuter],
            ['id' => Buildingtype::Commuter3x, 'name' => 'Commuter (3 meal)', 'image' => null, 'blurb' => null, 'side' => Buildingside::Commuter]
        ]);
    }
}
