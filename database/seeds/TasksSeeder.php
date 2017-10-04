<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class TasksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::all();
        $faker = Faker\Factory::create();
        foreach ($users as $user) {
            $projectIds = $user->projects()->pluck('id');
            for ($i=0; $i<6; $i++) {
                factory(App\Models\Task::class)->create([
                    'user_id' => $user->id,
                    'project_id' => $faker->numberBetween(
                        $projectIds->min(),
                        $projectIds->max()
                    )
                ]);
            }
        }
    }
}
