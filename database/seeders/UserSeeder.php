<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::query()->where('id', '=', 1)->delete();
        $user = new User();
        $user->id = 1;
        $user->name = 'admin';
        $user->email = 'admin@tg.test';
        $user->password = bcrypt('TG12345678');
        $user->telegram_id = '__admin__';
        $user->save();
    }
}
