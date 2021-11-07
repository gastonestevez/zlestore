<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ConceptSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('concepts')->insert([
            'name' => 'Venta en local'
        ]);
    }
}
