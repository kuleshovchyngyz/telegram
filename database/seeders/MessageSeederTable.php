<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class MessageSeederTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        for($i=0;$i<100;$i++){
            \DB::table('messages')->insert([
                'message' => $faker->sentence(),
                'status' => $faker->randomElement([false , true]),
                'company_id' => $faker->randomElement([1, 2]),
                'companycode' => $faker->uuid() ,
                'uniquecode' => $faker->uuid() ,
                'created_at' => \Carbon\Carbon::now(),
                'Updated_at' => \Carbon\Carbon::now(),
            ]);
        }

    }
}
