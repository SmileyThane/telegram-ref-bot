<?php

namespace Database\Seeders;

use App\Models\Referrer;
use Illuminate\Database\Seeder;

class RefSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ref = new Referrer();
        $ref->id = 1;
        $ref->name = 'Default Ref';
        $ref->link = 'test.com';
        $ref->save();
    }
}
