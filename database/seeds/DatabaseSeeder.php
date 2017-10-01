<?php

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'email' => 'kmaxat@gmail.com',
            'password' => bcrypt('qweqwe'),
            'name' => 'Maxat Ku',
        ]);
        factory(App\Models\User::class, 10)->create();
        factory(App\Models\Color::class, 12)->create();
        factory(App\Models\Project::class, 60)->create()->each(function ($u){
            $u->tasks()->saveMany(factory(App\Models\Task::class,10)->make());
        });
    }
}
