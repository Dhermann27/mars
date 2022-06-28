<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PronounSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $pronouns = array('M' => 'He/him',
            'F' => 'She/her',
            'T' => 'They/them',
            'A' => 'All/any',
            'X' => 'Xe/Xir',
            'Z' => 'Ze/Zir',
            'O' => 'Other',
            'N' => 'No pronouns',
            'K' => 'Ask me about my pronouns');
        foreach ($pronouns as $code => $name) {
            DB::table('pronouns')->insert([
                'code' => $code,
                'name' => $name
            ]);
        }
    }
}
