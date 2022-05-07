<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $provinces = array('AK' => 'Alaska',
            'AL' => 'Alabama',
            'AB' => 'Alberta',
            'AR' => 'Arkansas',
            'AZ' => 'Arizona',
            'BC' => 'British Columbia',
            'CA' => 'California',
            'CO' => 'Colorado',
            'CT' => 'Connecticut',
            'DC' => 'District of Columbia',
            'DE' => 'Delaware',
            'FL' => 'Florida',
            'GA' => 'Georgia',
            'HI' => 'Hawaii',
            'IA' => 'Iowa',
            'ID' => 'Idaho',
            'IL' => 'Illinois',
            'IN' => 'Indiana',
            'KS' => 'Kansas',
            'KY' => 'Kentucky',
            'LA' => 'Louisiana',
            'MB' => 'Manitoba',
            'MA' => 'Massachusetts',
            'MD' => 'Maryland',
            'ME' => 'Maine',
            'MI' => 'Michigan',
            'MN' => 'Minnesota',
            'MO' => 'Missouri',
            'MS' => 'Mississippi',
            'MT' => 'Montana',
            'NC' => 'North Carolina',
            'ND' => 'North Dakota',
            'NE' => 'Nebraska',
            'NB' => 'New Brunswick',
            'NH' => 'New Hampshire',
            'NJ' => 'New Jersey',
            'NM' => 'New Mexico',
            'NL' => 'Newfoundland',
            'NV' => 'Nevada',
            'NY' => 'New York',
            'NT' => 'Northwest Territories',
            'NS' => 'Nova Scotia',
            'NU' => 'Nunavut',
            'OH' => 'Ohio',
            'OK' => 'Oklahoma',
            'ON' => 'Ontario',
            'OR' => 'Oregon',
            'PA' => 'Pennsylvania',
            'PE' => 'Prince Edward Island',
            'QC' => 'Quebec',
            'RI' => 'Rhode Island',
            'SC' => 'South Carolina',
            'SD' => 'South Dakota',
            'SK' => 'Saskatchewan',
            'TN' => 'Tennessee',
            'TX' => 'Texas',
            'UT' => 'Utah',
            'VA' => 'Virginia',
            'VT' => 'Vermont',
            'WA' => 'Washington',
            'WI' => 'Wisconsin',
            'WV' => 'West Virginia',
            'WY' => 'Wyoming',
            'YU' => 'Yukon',
            '__' => 'ZZ - International');
        foreach ($provinces as $code => $name) {
            DB::table('provinces')->insert([
                'code' => $code,
                'name' => $name
            ]);
        }
    }
}
