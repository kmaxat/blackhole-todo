<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class LabelsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::all();
        foreach ($users as $user) {
            factory(App\Models\Label::class, 6)->create([
                'user_id' => $user->id
            ]);
        }
    }
}
