<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Status;
class StatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 生成内容
     * @return void
     */
    public function run()
    {
        $user_id = ['1','2','3'];
        $faker = app(Faker\Generator::class);
        $statuses = factory(Status::class)->times(100)->make()->each(function ($status) use ($faker, $user_id){
           $status->user_id = $faker->randomElement($user_id);
        });
        Status::insert($statuses->toArray());
    }
}
